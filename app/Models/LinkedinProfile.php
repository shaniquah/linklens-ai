<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedinProfile extends Model
{
    protected $fillable = [
        'user_id',
        'linkedin_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'profile_data',
        'auto_accept_connections',
        'post_automation_enabled',
    ];

    protected $casts = [
        'profile_data' => 'array',
        'token_expires_at' => 'datetime',
        'auto_accept_connections' => 'boolean',
        'post_automation_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}