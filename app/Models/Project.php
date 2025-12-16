<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    const STATUS_READY_TO_BEGIN = 'ready_to_begin';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_DONE = 'done';

    const STATUSES = [
        self::STATUS_READY_TO_BEGIN => 'Ready to Begin',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_ON_HOLD => 'On Hold',
        self::STATUS_DONE => 'Done',
    ];

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'hash',
        'status',
        'label_id',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($project) {
            if (empty($project->hash)) {
                $project->hash = static::generateUniqueHash();
            }
        });
    }

    public static function generateUniqueHash(): string
    {
        do {
            $hash = Str::random(12);
        } while (static::where('hash', $hash)->exists());
        
        return $hash;
    }

    public function getRouteKeyName()
    {
        return 'hash';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scheduledTasks(): HasMany
    {
        return $this->hasMany(Task::class)->scheduled();
    }

    public function unscheduledTasks(): HasMany
    {
        return $this->hasMany(Task::class)->unscheduled();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): array
    {
        return match($this->status) {
            self::STATUS_READY_TO_BEGIN => [
                'bg' => 'bg-gray-100 dark:bg-gray-700',
                'text' => 'text-gray-700 dark:text-gray-300',
                'border' => 'border-gray-300 dark:border-gray-600',
            ],
            self::STATUS_IN_PROGRESS => [
                'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                'text' => 'text-blue-700 dark:text-blue-300',
                'border' => 'border-blue-300 dark:border-blue-600',
            ],
            self::STATUS_ON_HOLD => [
                'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                'text' => 'text-yellow-700 dark:text-yellow-300',
                'border' => 'border-yellow-300 dark:border-yellow-600',
            ],
            self::STATUS_DONE => [
                'bg' => 'bg-green-100 dark:bg-green-900/30',
                'text' => 'text-green-700 dark:text-green-300',
                'border' => 'border-green-300 dark:border-green-600',
            ],
            default => [
                'bg' => 'bg-gray-100 dark:bg-gray-700',
                'text' => 'text-gray-700 dark:text-gray-300',
                'border' => 'border-gray-300 dark:border-gray-600',
            ],
        };
    }

    public function checkAndUpdateCompletionStatus(): bool
    {
        // Get all subtasks (tasks with a parent)
        $subtasks = $this->tasks()->whereNotNull('parent_id')->get();

        if ($subtasks->isEmpty()) {
            return false;
        }

        $allCompleted = $subtasks->every(fn($task) => $task->completed);

        if ($allCompleted && $this->status !== self::STATUS_DONE) {
            $this->update(['status' => self::STATUS_DONE]);
            return true;
        }

        return false;
    }
}
