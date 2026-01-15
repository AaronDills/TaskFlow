<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'github_pr_url',
        'ai_response',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public const TYPE_FEEDBACK = 'feedback';
    public const TYPE_FEATURE_REQUEST = 'feature_request';
    public const TYPE_BUG_REPORT = 'bug_report';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsCompleted(?string $prUrl = null, ?string $aiResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'github_pr_url' => $prUrl,
            'ai_response' => $aiResponse,
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(?string $aiResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'ai_response' => $aiResponse,
            'processed_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
