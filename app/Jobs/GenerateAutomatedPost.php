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
        private LinkedinProfile $profile,
        private array $settings = []
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
            'status' => $this->settings['approval_required'] ?? false ? 'pending' : 'ready',
            'scheduled_at' => $this->calculateScheduleTime(),
        ]);
        
        // Broadcast the new post
        \App\Events\PostCreated::dispatch($post);

        // Only auto-publish if approval not required
        if (!($this->settings['approval_required'] ?? false)) {
            $this->publishPost($post);
        }
    }

    private function generatePostContent(): string
    {
        $voice = $this->settings['voice'] ?? 'professional';
        $tone = $this->settings['tone'] ?? 'informative';
        $themes = $this->settings['themes'] ?? ['industry_insights'];
        $diction = $this->settings['diction'] ?? 'business';
        
        $themeTemplates = [
            'industry_insights' => [
                'The future of {industry} is being shaped by {trend}. Here\'s what professionals need to know...',
                'Key insight: {insight} is transforming how we approach {area}.',
            ],
            'career_tips' => [
                'Career tip: {tip} can accelerate your professional growth.',
                'One strategy that changed my career trajectory: {strategy}',
            ],
            'networking' => [
                'Networking isn\'t about collecting contacts—it\'s about {value}.',
                'The best networking happens when you {approach}.',
            ],
            'motivation' => [
                'Remember: {motivation_quote}',
                'Every challenge is an opportunity to {growth_area}.',
            ],
        ];
        
        $selectedTheme = $themes[array_rand($themes)];
        $templates = $themeTemplates[$selectedTheme] ?? $themeTemplates['industry_insights'];
        $template = $templates[array_rand($templates)];
        
        // Simple template filling - in production, use OpenAI API
        $content = str_replace(
            ['{industry}', '{trend}', '{insight}', '{area}', '{tip}', '{strategy}', '{value}', '{approach}', '{motivation_quote}', '{growth_area}'],
            ['technology', 'AI automation', 'Data-driven decision making', 'professional development', 'continuous learning', 'building authentic relationships', 'creating mutual value', 'focus on helping others first', 'Success is a journey, not a destination', 'develop new skills'],
            $template
        );
        
        $emoji = match($tone) {
            'inspirational' => '✨',
            'educational' => '📚',
            'promotional' => '🚀',
            default => '💡'
        };
        
        return $emoji . ' ' . $content . ' #LinkedIn #Professional #Growth';
    }
    
    private function calculateScheduleTime(): \Carbon\Carbon
    {
        $frequency = $this->settings['frequency'] ?? 'daily';
        
        return match($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'bi-weekly' => now()->addWeeks(2),
            default => now()->addDay()
        };
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