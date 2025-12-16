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

    const COLOR_CLASSES = [
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/30',
            'text' => 'text-blue-700 dark:text-blue-300',
            'border' => 'border-blue-300 dark:border-blue-600',
        ],
        'green' => [
            'bg' => 'bg-green-100 dark:bg-green-900/30',
            'text' => 'text-green-700 dark:text-green-300',
            'border' => 'border-green-300 dark:border-green-600',
        ],
        'red' => [
            'bg' => 'bg-red-100 dark:bg-red-900/30',
            'text' => 'text-red-700 dark:text-red-300',
            'border' => 'border-red-300 dark:border-red-600',
        ],
        'yellow' => [
            'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
            'text' => 'text-yellow-700 dark:text-yellow-300',
            'border' => 'border-yellow-300 dark:border-yellow-600',
        ],
        'purple' => [
            'bg' => 'bg-purple-100 dark:bg-purple-900/30',
            'text' => 'text-purple-700 dark:text-purple-300',
            'border' => 'border-purple-300 dark:border-purple-600',
        ],
        'pink' => [
            'bg' => 'bg-pink-100 dark:bg-pink-900/30',
            'text' => 'text-pink-700 dark:text-pink-300',
            'border' => 'border-pink-300 dark:border-pink-600',
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-900/30',
            'text' => 'text-indigo-700 dark:text-indigo-300',
            'border' => 'border-indigo-300 dark:border-indigo-600',
        ],
        'gray' => [
            'bg' => 'bg-gray-100 dark:bg-gray-900/30',
            'text' => 'text-gray-700 dark:text-gray-300',
            'border' => 'border-gray-300 dark:border-gray-600',
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
