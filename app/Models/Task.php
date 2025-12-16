<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'parent_id',
        'title',
        'category',
        'priority',
        'due_date',
        'deadline',
        'is_parent',
        'on_hold',
        'description',
        'completed',
        'completed_at',
        'order',
        'scheduled_date',
        'scheduled_time',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'is_parent' => 'boolean',
        'on_hold' => 'boolean',
        'scheduled_date' => 'date',
        'due_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (Task $task) {
            // Automatically set completed_at when task is marked as completed
            if ($task->isDirty('completed')) {
                if ($task->completed) {
                    $task->completed_at = now();
                } else {
                    $task->completed_at = null;
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Parent task relationship (for subtasks)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // Subtasks relationship (for parent tasks)
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // Scope for parent tasks only
    public function scopeParentTasks($query)
    {
        return $query->where('is_parent', true);
    }

    // Scope for subtasks only
    public function scopeSubtasks($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Scope for standalone tasks (not associated with any project)
    public function scopeStandalone($query)
    {
        return $query->whereNull('project_id')->whereNull('parent_id');
    }

    // Scope for prioritized subtasks (for Recommended section)
    public function scopePrioritized($query)
    {
        return $query
            ->whereNotNull('parent_id')
            ->where('completed', false)
            ->orderByRaw("CASE
                WHEN due_date IS NOT NULL AND due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priority
                WHEN 'high' THEN 1
                WHEN 'med' THEN 2
                WHEN 'low' THEN 3 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'asc');
    }

    // Scope for scheduled tasks
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_date')->whereNotNull('scheduled_time');
    }

    // Scope for unscheduled tasks
    public function scopeUnscheduled($query)
    {
        return $query->whereNull('scheduled_date')->whereNull('scheduled_time');
    }

    // Scope for tasks scheduled on a specific date
    public function scopeScheduledOn($query, $date)
    {
        return $query->scheduled()->whereDate('scheduled_date', $date);
    }

    // Check if task is scheduled
    public function isScheduled(): bool
    {
        return !is_null($this->scheduled_date) && !is_null($this->scheduled_time);
    }

    // Check if this is a subtask
    public function isSubtask(): bool
    {
        return !is_null($this->parent_id);
    }

    // Auto-complete parent task if all subtasks are done
    public function checkAndUpdateParentCompletion(): void
    {
        if (!$this->parent_id) {
            return;
        }

        $parent = $this->parent;
        if (!$parent) {
            return;
        }

        $allSubtasksComplete = $parent->subtasks()->where('completed', false)->count() === 0;

        if ($allSubtasksComplete && !$parent->completed) {
            $parent->update(['completed' => true]);
        } elseif (!$allSubtasksComplete && $parent->completed) {
            $parent->update(['completed' => false]);
        }
    }

    // Get completion progress for parent tasks
    public function getCompletionProgress(): array
    {
        if (!$this->is_parent) {
            return ['completed' => 0, 'total' => 0];
        }

        $total = $this->subtasks()->count();
        $completed = $this->subtasks()->where('completed', true)->count();

        return ['completed' => $completed, 'total' => $total];
    }
}
