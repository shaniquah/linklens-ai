<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectionFilter extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'criteria',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matchesProfile(array $profile): bool
    {
        foreach ($this->criteria as $key => $value) {
            if (!$this->checkCriteria($profile, $key, $value)) {
                return false;
            }
        }
        return true;
    }

    private function checkCriteria(array $profile, string $key, $value): bool
    {
        return match ($key) {
            'industry' => in_array($profile['industry'] ?? '', (array) $value),
            'location' => str_contains(strtolower($profile['location'] ?? ''), strtolower($value)),
            'job_title' => str_contains(strtolower($profile['job_title'] ?? ''), strtolower($value)),
            'company_size' => ($profile['company_size'] ?? 0) >= $value,
            default => true,
        };
    }
}