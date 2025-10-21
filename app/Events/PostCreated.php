<?php

namespace App\Events;

use App\Models\AutomatedPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast
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
            'post' => [
                'id' => $this->post->id,
                'content' => $this->post->content,
                'status' => $this->post->status,
                'created_at' => $this->post->created_at->diffForHumans(),
            ]
        ];
    }
}