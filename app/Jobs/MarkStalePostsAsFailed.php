<?php

namespace App\Jobs;

use App\Events\PostStale;
use App\Models\AutomatedPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkStalePostsAsFailed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $stalePosts = AutomatedPost::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->get();

        foreach ($stalePosts as $post) {
            $post->increment('retry_count');
            
            if ($post->retry_count >= 5) {
                $post->update(['status' => 'failed']);
            } else {
                $post->update(['status' => 'ready']);
                GenerateAutomatedPost::dispatch($post->user->linkedinProfile, [
                    'retry' => true,
                    'post_id' => $post->id
                ]);
            }
            
            PostStale::dispatch($post);
        }
    }
}