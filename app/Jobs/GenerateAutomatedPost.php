<?php

namespace App\Jobs;

use App\Models\AutomatedPost;
use App\Models\LinkedinProfile;
use App\Services\SimplifiedBedrockService;
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

    private SimplifiedBedrockService $bedrockService;

    public function handle(SimplifiedBedrockService $bedrockService): void
    {
        $this->bedrockService = $bedrockService;

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

        // Handle retry scenario
        if (isset($this->settings['retry']) && isset($this->settings['post_id'])) {
            $existingPost = AutomatedPost::find($this->settings['post_id']);
            if ($existingPost && $existingPost->canRetry()) {
                $this->retryPost($existingPost);
                return;
            }
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
        // Use AWS Bedrock for AI-powered content generation
        $content = $this->bedrockService->generateLinkedInPost($this->settings);
        
        // Add emoji based on tone
        $tone = $this->settings['tone'] ?? 'informative';
        $emoji = match($tone) {
            'inspirational' => '✨',
            'educational' => '📚',
            'promotional' => '🚀',
            default => '💡'
        };

        return $emoji . ' ' . $content;
    }
    
    private function retryPost(AutomatedPost $post): void
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

    private function generateShortPost($theme, $tone): string
    {
        $templates = [
            'industry_insights' => 'AI is reshaping how we work.',
            'career_tips' => 'Network before you need it.',
            'networking' => 'Quality connections over quantity.',
            'motivation' => 'Every setback is a setup for a comeback.',
        ];

        return $templates[$theme] ?? $templates['industry_insights'];
    }

    private function generateMediumPost($theme, $tone): string
    {
        $templates = [
            'industry_insights' => 'The future of technology is being shaped by AI automation. Here\'s what professionals need to know: adapt quickly, learn continuously, and embrace change as your competitive advantage.',
            'career_tips' => 'Career tip: Build your network before you need it. The best opportunities often come through relationships, not job boards. Invest time in genuine connections today.',
            'networking' => 'Networking isn\'t about collecting contacts—it\'s about creating mutual value. Focus on how you can help others first, and watch your professional relationships flourish.',
            'motivation' => 'Remember: Success is a journey, not a destination. Every challenge you face today is building the resilience you\'ll need for tomorrow\'s opportunities.',
        ];

        return $templates[$theme] ?? $templates['industry_insights'];
    }

    private function generateLongPost($theme, $tone): string
    {
        $templates = [
            'industry_insights' => "The AI revolution isn't coming—it's here. And it's transforming every industry at an unprecedented pace.\n\nWhat does this mean for professionals?\n\n• Routine tasks are being automated\n• New roles are emerging that didn't exist 5 years ago\n• The half-life of skills is shrinking rapidly\n\nThe key to thriving in this environment? Continuous learning and adaptability.\n\nStart by identifying which aspects of your role can be enhanced (not replaced) by AI. Then, focus on developing uniquely human skills: creativity, emotional intelligence, complex problem-solving, and strategic thinking.\n\nThe future belongs to those who can work alongside AI, not against it.",
            'career_tips' => "Here's the career advice I wish someone had given me 10 years ago:\n\nYour network is your net worth—but not in the way you think.\n\nIt's not about having 10,000 LinkedIn connections. It's about having 10 people who:\n• Know your work quality\n• Trust your character\n• Understand your goals\n• Will advocate for you when you're not in the room\n\nHow do you build this kind of network?\n\n1. Be genuinely helpful to others\n2. Share knowledge freely\n3. Celebrate others' successes\n4. Stay in touch consistently\n5. Be authentic in all interactions\n\nRemember: People don't refer strangers. They refer people they know, like, and trust.",
            'networking' => "The biggest networking mistake I see professionals make?\n\nTreating networking like a transaction.\n\n'Hi, I'm looking for a job. Can you help?'\n\nThis approach fails because it's entirely one-sided.\n\nEffective networking is about building relationships, not collecting favors.\n\nHere's a better approach:\n\n1. Lead with curiosity, not need\n2. Ask thoughtful questions about their work\n3. Share relevant insights or resources\n4. Follow up with value, not requests\n5. Maintain relationships during good times\n\nWhen you focus on giving first, receiving becomes natural.\n\nThe best networkers are the best givers.",
            'motivation' => "Failure is not the opposite of success—it's a stepping stone to it.\n\nEvery successful person has a graveyard of failed attempts behind them. The difference? They didn't let failure define them.\n\nHere's what I've learned about resilience:\n\n• Failure is feedback, not a verdict\n• Every setback teaches you something valuable\n• Your response to failure matters more than the failure itself\n• Persistence beats perfection every time\n\nThe next time you face a setback, ask yourself:\n1. What can I learn from this?\n2. How can I improve next time?\n3. What opportunity might this create?\n\nYour greatest comeback is always ahead of you, not behind you."
        ];

        return $templates[$theme] ?? $templates['industry_insights'];
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
