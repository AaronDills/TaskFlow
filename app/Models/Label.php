<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends Model
{
    protected $fillable = ['user_id', 'name', 'color'];

    const COLORS = [
        'blue', 'green', 'red', 'yellow', 'purple', 'pink', 'indigo', 'gray'
    ];

    // Color classes optimized for WCAG 2.1 AA compliance (4.5:1 contrast ratio)
    const COLOR_CLASSES = [
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/50',
            'text' => 'text-blue-800 dark:text-blue-100',
            'border' => 'border-blue-300 dark:border-blue-500',
        ],
        'green' => [
            'bg' => 'bg-green-100 dark:bg-green-900/50',
            'text' => 'text-green-800 dark:text-green-100',
            'border' => 'border-green-300 dark:border-green-500',
        ],
        'red' => [
            'bg' => 'bg-red-100 dark:bg-red-900/50',
            'text' => 'text-red-800 dark:text-red-100',
            'border' => 'border-red-300 dark:border-red-500',
        ],
        'yellow' => [
            'bg' => 'bg-yellow-100 dark:bg-yellow-900/50',
            'text' => 'text-yellow-900 dark:text-yellow-100',
            'border' => 'border-yellow-400 dark:border-yellow-500',
        ],
        'purple' => [
            'bg' => 'bg-purple-100 dark:bg-purple-900/50',
            'text' => 'text-purple-800 dark:text-purple-100',
            'border' => 'border-purple-300 dark:border-purple-500',
        ],
        'pink' => [
            'bg' => 'bg-pink-100 dark:bg-pink-900/50',
            'text' => 'text-pink-800 dark:text-pink-100',
            'border' => 'border-pink-300 dark:border-pink-500',
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-900/50',
            'text' => 'text-indigo-800 dark:text-indigo-100',
            'border' => 'border-indigo-300 dark:border-indigo-500',
        ],
        'gray' => [
            'bg' => 'bg-gray-100 dark:bg-gray-800/50',
            'text' => 'text-gray-800 dark:text-gray-100',
            'border' => 'border-gray-300 dark:border-gray-500',
        ],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getColorClassesAttribute(): array
    {
        return self::COLOR_CLASSES[$this->color] ?? self::COLOR_CLASSES['blue'];
    }

    public function getBgClassAttribute(): string
    {
        return $this->colorClasses['bg'];
    }

    public function getTextClassAttribute(): string
    {
        return $this->colorClasses['text'];
    }

    public function getBorderClassAttribute(): string
    {
        return $this->colorClasses['border'];
    }
}
