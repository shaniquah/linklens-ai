<?php

namespace App\Jobs;

use App\Models\AutomatedPost;
use App\Models\LinkedinProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateAutomatedPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private LinkedinProfile $profile
    ) {}

    public function handle(): void
    {
        if (!$this->profile->post_automation_enabled) {
            return;
        }

        $dailyLimit = config('app.daily_post_limit', 3);
        $todayPosts = AutomatedPost::where('user_id', $this->profile->user_id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayPosts >= $dailyLimit) {
            return;
        }

        $content = $this->generatePostContent();
        
        $post = AutomatedPost::create([
            'user_id' => $this->profile->user_id,
            'content' => $content,
            'scheduled_at' => now(),
        ]);

        $this->publishPost($post);
    }

    private function generatePostContent(): string
    {
        $prompts = [
            "Share a professional insight about industry trends",
            "Discuss the importance of networking in career growth",
            "Share a motivational quote about professional development",
            "Highlight the value of continuous learning",
            "Discuss work-life balance strategies",
        ];

        $prompt = $prompts[array_rand($prompts)];
        
        // Simple content generation - in production, use OpenAI API
        return "🚀 " . $prompt . " #Professional #Growth #LinkedIn";
    }

    private function publishPost(AutomatedPost $post): void
    {
        $response = Http::withToken($this->profile->access_token)
            ->post('https://api.linkedin.com/v2/ugcPosts', [
                'author' => "urn:li:person:{$this->profile->linkedin_id}",
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => ['text' => $post->content],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => ['com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'],
            ]);

        if ($response->successful()) {
            $post->markAsPosted();
        } else {
            $post->markAsFailed();
        }
    }
}