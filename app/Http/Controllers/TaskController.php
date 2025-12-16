<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // If date parameter is provided, return scheduled tasks for that date (replacing time slot functionality)
        if ($request->has('date')) {
            $date = $request->get('date');
            $tasks = $project->tasks()
                ->scheduledOn($date)
                ->orderBy('scheduled_time')
                ->get()
                ->mapWithKeys(function ($task) {
                    // Handle both string and Carbon instances for scheduled_time
                    $timeKey = is_string($task->scheduled_time) 
                        ? $task->scheduled_time 
                        : $task->scheduled_time->format('H:i');
                    return [$timeKey => $task];
                });
            
            return response()->json($tasks);
        }

        // Return unscheduled tasks grouped by category
        $tasks = $project->tasks()->unscheduled()->orderBy('order')->orderBy('created_at')->get();
        return response()->json($tasks);
    }

    public function store(Request $request, Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required_without:content|string|max:255',
            'category' => 'sometimes|string|in:must,may,recommended,scheduled',
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            // For backward compatibility with time slot creation
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'content' => 'required_without:title|string',
        ]);

        // Handle time slot creation (backward compatibility)
        if ($request->has('date') && $request->has('time') && $request->has('content')) {
            $task = $project->tasks()->updateOrCreate(
                [
                    'scheduled_date' => $request->date,
                    'scheduled_time' => $request->time,
                ],
                [
                    'title' => $request->content ?: 'Scheduled Task',
                    'category' => 'scheduled',
                    'description' => null,
                    'completed' => false,
                    'order' => 0,
                ]
            );
        } else {
            // Regular task creation
            $nextOrder = $project->tasks()
                ->where('category', $request->category ?? 'must')
                ->max('order') + 1;

            $task = $project->tasks()->create([
                'title' => $request->title,
                'category' => $request->category ?? 'must',
                'description' => $request->description,
                'order' => $nextOrder,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
            ]);
        }

        return response()->json($task, 201);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        // Ensure user owns the project and task belongs to project
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|string|in:must,may,recommended,scheduled',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);

        $task->update($request->only(['title', 'category', 'description', 'completed', 'scheduled_date', 'scheduled_time']));

        return response()->json($task);
    }

    public function destroy(Project $project, Task $task)
    {
        // Ensure user owns the project and task belongs to project
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    public function deleteByDateTime(Request $request, Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $task = $project->tasks()
            ->where('scheduled_date', $request->date)
            ->where('scheduled_time', $request->time)
            ->first();

        if ($task) {
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Scheduled task deleted']);
        }

        return response()->json(['success' => true, 'message' => 'Scheduled task not found (already deleted)']);
    }
}
