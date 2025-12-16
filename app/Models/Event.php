<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'color',
        'recurrence',
        'recurrence_end_date',
        'parent_event_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'recurrence_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function recurringInstances(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_datetime', [$startDate, $endDate])
              ->orWhereBetween('end_datetime', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_datetime', '<=', $startDate)
                     ->where('end_datetime', '>=', $endDate);
              });
        });
    }

    public function scopeOnDate($query, $date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        return $query->where(function ($q) use ($startOfDay, $endOfDay) {
            $q->whereBetween('start_datetime', [$startOfDay, $endOfDay])
              ->orWhereBetween('end_datetime', [$startOfDay, $endOfDay])
              ->orWhere(function ($q2) use ($startOfDay, $endOfDay) {
                  $q2->where('start_datetime', '<=', $startOfDay)
                     ->where('end_datetime', '>=', $endOfDay);
              });
        });
    }

    public function getDurationInMinutes(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }

    public function getTimeSlotCount(): int
    {
        return (int) ceil($this->getDurationInMinutes() / 15);
    }

    public function spansMultipleDays(): bool
    {
        return !$this->start_datetime->isSameDay($this->end_datetime);
    }

    public function getColorClasses(): array
    {
        $colors = [
            'blue'   => ['bg' => 'bg-blue-500', 'text' => 'text-blue-800', 'light' => 'bg-blue-100', 'border' => 'border-blue-500'],
            'green'  => ['bg' => 'bg-green-500', 'text' => 'text-green-800', 'light' => 'bg-green-100', 'border' => 'border-green-500'],
            'red'    => ['bg' => 'bg-red-500', 'text' => 'text-red-800', 'light' => 'bg-red-100', 'border' => 'border-red-500'],
            'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-800', 'light' => 'bg-purple-100', 'border' => 'border-purple-500'],
            'yellow' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-800', 'light' => 'bg-yellow-100', 'border' => 'border-yellow-500'],
            'pink'   => ['bg' => 'bg-pink-500', 'text' => 'text-pink-800', 'light' => 'bg-pink-100', 'border' => 'border-pink-500'],
            'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-800', 'light' => 'bg-indigo-100', 'border' => 'border-indigo-500'],
            'gray'   => ['bg' => 'bg-gray-500', 'text' => 'text-gray-800', 'light' => 'bg-gray-100', 'border' => 'border-gray-500'],
        ];

        return $colors[$this->color] ?? $colors['blue'];
    }
}
