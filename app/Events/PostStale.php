<?php

namespace App\Events;

use App\Models\AutomatedPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostStale implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AutomatedPost $post
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->post->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'post_id' => $this->post->id,
            'minutes_pending' => $this->post->created_at->diffInMinutes(now()),
            'retry_count' => $this->post->retry_count,
        ];
    }
}