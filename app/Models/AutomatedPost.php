<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedPost extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'status',
        'scheduled_at',
        'posted_at',
        'engagement_data',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'posted_at' => 'datetime',
        'engagement_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsPosted(): void
    {
        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}