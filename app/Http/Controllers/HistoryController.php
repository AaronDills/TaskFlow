<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $projects = Auth::user()->projects()->latest()->get();

        return view('history', compact('projects'));
    }

    public function getWeekData(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        $weekStart = Carbon::parse($request->week_start)->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $userId = Auth::id();
        $userProjectIds = Project::where('user_id', $userId)->pluck('id');

        // Get completed tasks for the week
        $completedTasks = Task::where(function ($query) use ($userId, $userProjectIds) {
                $query->where('user_id', $userId)
                    ->orWhereIn('project_id', $userProjectIds);
            })
            ->where('completed', true)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$weekStart, $weekEnd])
            ->with('project')
            ->orderBy('completed_at', 'desc')
            ->get();

        // Get created tasks for the week (for metrics)
        $createdTasks = Task::where(function ($query) use ($userId, $userProjectIds) {
                $query->where('user_id', $userId)
                    ->orWhereIn('project_id', $userProjectIds);
            })
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Group tasks by day
        $tasksByDay = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $dayTasks = $completedTasks->filter(function ($task) use ($date) {
                return $task->completed_at->isSameDay($date);
            })->values();

            $tasksByDay[$dateStr] = [
                'date' => $dateStr,
                'dayName' => $date->format('l'),
                'dayShort' => $date->format('D'),
                'dayNumber' => $date->format('j'),
                'month' => $date->format('M'),
                'isToday' => $date->isToday(),
                'tasks' => $dayTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'project_name' => $task->project?->name,
                        'project_hash' => $task->project?->hash,
                        'completed_at' => $task->completed_at->format('g:i A'),
                        'priority' => $task->priority,
                        'is_subtask' => $task->parent_id !== null,
                    ];
                }),
                'completedCount' => $dayTasks->count(),
            ];
        }

        // Calculate week metrics
        $totalCompleted = $completedTasks->count();
        $totalCreated = $createdTasks;
        $averagePerDay = $totalCompleted > 0 ? round($totalCompleted / 7, 1) : 0;

        // Get completion streak
        $streak = $this->calculateStreak($userId, $userProjectIds, $weekStart);

        return response()->json([
            'weekStart' => $weekStart->format('Y-m-d'),
            'weekEnd' => $weekEnd->format('Y-m-d'),
            'weekLabel' => $weekStart->format('M j') . ' - ' . $weekEnd->format('M j, Y'),
            'tasksByDay' => $tasksByDay,
            'metrics' => [
                'totalCompleted' => $totalCompleted,
                'totalCreated' => $totalCreated,
                'averagePerDay' => $averagePerDay,
                'streak' => $streak,
            ],
        ]);
    }

    private function calculateStreak($userId, $userProjectIds, $weekStart)
    {
        $streak = 0;
        $checkDate = Carbon::today();

        while (true) {
            $dayStart = $checkDate->copy()->startOfDay();
            $dayEnd = $checkDate->copy()->endOfDay();

            $completedOnDay = Task::where(function ($query) use ($userId, $userProjectIds) {
                    $query->where('user_id', $userId)
                        ->orWhereIn('project_id', $userProjectIds);
                })
                ->where('completed', true)
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$dayStart, $dayEnd])
                ->exists();

            if ($completedOnDay) {
                $streak++;
                $checkDate->subDay();
            } else {
                // If today has no completions, check if we can still start a streak from yesterday
                if ($checkDate->isToday()) {
                    $checkDate->subDay();
                    continue;
                }
                break;
            }

            // Safety limit
            if ($streak > 365) break;
        }

        return $streak;
    }
}
