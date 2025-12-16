<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display the unified project page (tasks + details).
     */
    public function index(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $parentTasks = $project->tasks()
            ->parentTasks()
            ->with(['subtasks' => function ($query) {
                $query->orderBy('order')->orderBy('created_at');
            }])
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();

        $projects = Auth::user()->projects()->latest()->get();

        // Load task statistics (from show method)
        $stats = [
            'total' => $project->tasks()->count(),
            'completed' => $project->tasks()->where('completed', true)->count(),
            'must' => $project->tasks()->where('category', 'must')->count(),
            'may' => $project->tasks()->where('category', 'may')->count(),
            'recommended' => $project->tasks()->where('category', 'recommended')->count(),
        ];

        // Load labels for label management
        $labels = Auth::user()->labels()->orderBy('name')->get();

        // Load label for current project
        $project->load('label');

        return view('projects.tasks', compact('project', 'parentTasks', 'projects', 'stats', 'labels'));
    }

    /**
     * Store a new parent task.
     */
    public function storeParentTask(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $task = $project->tasks()->create([
            'title' => $request->title,
            'deadline' => $request->deadline,
            'is_parent' => true,
            'category' => 'recommended',
            'order' => $project->tasks()->parentTasks()->count(),
        ]);

        return response()->json($task->load('subtasks'));
    }

    /**
     * Update a parent task.
     */
    public function updateParentTask(Request $request, Project $project, Task $task)
    {
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $task->update([
            'title' => $request->title,
            'deadline' => $request->deadline,
        ]);

        return response()->json($task->load('subtasks'));
    }

    /**
     * Delete a parent task (cascades to subtasks).
     */
    public function destroyParentTask(Project $project, Task $task)
    {
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Store a new subtask under a parent task.
     */
    public function storeSubtask(Request $request, Project $project, Task $parentTask)
    {
        if ($project->user_id !== Auth::id() || $parentTask->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'in:high,med,low',
            'due_date' => 'nullable|date',
        ]);

        // Auto-apply parent deadline to subtask due_date if not specified
        $dueDate = $request->due_date;
        if (!$dueDate && $parentTask->deadline) {
            $dueDate = $parentTask->deadline->format('Y-m-d');
        }

        $subtask = $project->tasks()->create([
            'parent_id' => $parentTask->id,
            'title' => $request->title,
            'priority' => $request->priority ?? 'med',
            'due_date' => $dueDate,
            'category' => 'recommended',
            'order' => $parentTask->subtasks()->count(),
        ]);

        return response()->json($subtask);
    }

    /**
     * Update a subtask.
     */
    public function updateSubtask(Request $request, Project $project, Task $subtask)
    {
        if ($project->user_id !== Auth::id() || $subtask->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'priority' => 'sometimes|in:high,med,low',
            'due_date' => 'nullable|date',
            'completed' => 'sometimes|boolean',
        ]);

        $subtask->update($request->only(['title', 'priority', 'due_date', 'completed']));

        $projectCompleted = false;

        // Check if parent should be auto-completed
        if ($request->has('completed')) {
            $subtask->checkAndUpdateParentCompletion();

            // Check if project should auto-transition to Done
            if ($request->completed && $project) {
                $projectCompleted = $project->checkAndUpdateCompletionStatus();
            }
        }

        return response()->json([
            'subtask' => $subtask->fresh(),
            'project_completed' => $projectCompleted,
        ]);
    }

    /**
     * Delete a subtask.
     */
    public function destroySubtask(Project $project, Task $subtask)
    {
        if ($project->user_id !== Auth::id() || $subtask->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $parentId = $subtask->parent_id;
        $subtask->delete();

        // Check if parent should update completion status
        if ($parentId) {
            $parent = Task::find($parentId);
            if ($parent) {
                $allComplete = $parent->subtasks()->where('completed', false)->count() === 0;
                if ($allComplete && $parent->subtasks()->count() === 0) {
                    // No subtasks left, mark parent as incomplete
                    $parent->update(['completed' => false]);
                } elseif ($allComplete) {
                    $parent->update(['completed' => true]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get top 3 prioritized subtasks for the Recommended section.
     */
    public function recommended(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subtasks = $project->tasks()
            ->prioritized()
            ->with('parent:id,title')
            ->limit(3)
            ->get();

        return response()->json($subtasks);
    }

    /**
     * Mark a subtask as complete and return updated recommended list.
     */
    public function complete(Request $request, Project $project, Task $subtask)
    {
        if ($project->user_id !== Auth::id() || $subtask->project_id !== $project->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subtask->update(['completed' => true]);
        $subtask->checkAndUpdateParentCompletion();

        // Check if project should auto-transition to Done
        $project->checkAndUpdateCompletionStatus();

        // Return updated recommended list
        $recommended = $project->tasks()
            ->prioritized()
            ->with('parent:id,title')
            ->limit(3)
            ->get();

        return response()->json([
            'subtask' => $subtask->fresh(),
            'recommended' => $recommended,
        ]);
    }

    /**
     * Get global recommended subtasks from all user's projects.
     * Excludes tasks moved to Must Do/May Do (category != 'recommended').
     * Auto-fills to 3 tasks when possible.
     */
    public function globalRecommended()
    {
        $user = Auth::user();
        $projectIds = $user->projects()->pluck('id');

        // Get prioritized subtasks that are still in recommended category
        $subtasks = Task::whereIn('project_id', $projectIds)
            ->whereNotNull('parent_id')
            ->where('completed', false)
            ->where(function ($query) {
                // Only include tasks in recommended category (not moved to must/may)
                $query->where('category', 'recommended')
                    ->orWhereNull('category');
            })
            ->orderByRaw("CASE
                WHEN due_date IS NOT NULL AND due_date <= date('now', '+7 days')
                THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priority
                WHEN 'high' THEN 1
                WHEN 'med' THEN 2
                WHEN 'low' THEN 3 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->with(['parent:id,title', 'project:id,name'])
            ->limit(3)
            ->get();

        return response()->json($subtasks);
    }

    /**
     * Complete a subtask globally and return updated recommended list.
     */
    public function completeGlobalSubtask(Task $subtask)
    {
        $user = Auth::user();

        // Verify ownership through project or user_id
        if ($subtask->project_id) {
            $project = Project::find($subtask->project_id);
            if (!$project || $project->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($subtask->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subtask->update(['completed' => true]);
        $subtask->checkAndUpdateParentCompletion();

        // Check if project should auto-transition to Done
        if ($subtask->project_id) {
            $project = Project::find($subtask->project_id);
            if ($project) {
                $project->checkAndUpdateCompletionStatus();
            }
        }

        // Return updated global recommended list
        $projectIds = $user->projects()->pluck('id');
        $recommended = Task::whereIn('project_id', $projectIds)
            ->prioritized()
            ->with(['parent:id,title', 'project:id,name'])
            ->limit(3)
            ->get();

        return response()->json([
            'subtask' => $subtask->fresh(),
            'recommended' => $recommended,
        ]);
    }

    /**
     * Get tasks for Must Do / May Do sections.
     * Includes both standalone tasks and moved project tasks.
     */
    public function getStandaloneTasks()
    {
        $user = Auth::user();
        $projectIds = $user->projects()->pluck('id');

        // Get standalone tasks (no project) and moved project tasks (have project but category is must/may)
        // Only show tasks scheduled for today - each day starts fresh
        $today = now()->toDateString();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $tasks = Task::where(function ($query) use ($user, $projectIds) {
                $query->where(function ($q) use ($user) {
                        // Standalone tasks
                        $q->where('user_id', $user->id)
                            ->whereNull('project_id')
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($q) use ($projectIds) {
                        // Moved project tasks
                        $q->whereIn('project_id', $projectIds)
                            ->whereNotNull('parent_id')
                            ->whereIn('category', ['must', 'may']);
                    });
            })
            ->where(function ($query) use ($today, $todayStart, $todayEnd) {
                // Tasks with scheduled_date = today, OR tasks created today without scheduled_date
                $query->where('scheduled_date', $today)
                    ->orWhere(function ($q) use ($todayStart, $todayEnd) {
                        $q->whereNull('scheduled_date')
                            ->whereBetween('created_at', [$todayStart, $todayEnd]);
                    });
            })
            ->orderBy('completed') // Show incomplete tasks first
            ->orderBy('order')
            ->orderBy('created_at')
            ->get()
            ->map(function ($task) {
                // Add flag to indicate if this is a project task (can be moved back to recommended)
                $task->is_project_task = !is_null($task->project_id) && !is_null($task->parent_id);
                return $task;
            })
            ->groupBy('category');

        return response()->json([
            'must' => $tasks->get('must', collect())->values(),
            'may' => $tasks->get('may', collect())->values(),
        ]);
    }

    /**
     * Store a new standalone task (Must Do / May Do).
     */
    public function storeStandaloneTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:must,may',
        ]);

        $user = Auth::user();

        $maxOrder = Task::where('user_id', $user->id)
            ->standalone()
            ->where('category', $request->category)
            ->max('order') ?? -1;

        $task = Task::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'category' => $request->category,
            'order' => $maxOrder + 1,
            'priority' => 'med',
            'scheduled_date' => now()->toDateString(), // Tasks only show on the day they're created
        ]);

        return response()->json($task);
    }

    /**
     * Update a standalone task.
     */
    public function updateStandaloneTask(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|in:must,may',
            'order' => 'sometimes|integer',
            'completed' => 'sometimes|boolean',
        ]);

        $task->update($request->only(['title', 'category', 'order', 'completed']));

        return response()->json($task->fresh());
    }

    /**
     * Delete a standalone task.
     */
    public function destroyStandaloneTask(Task $task)
    {
        $user = Auth::user();

        if ($task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reorder tasks in Must Do / May Do sections.
     */
    public function reorderTasks(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|integer',
            'tasks.*.order' => 'required|integer',
            'tasks.*.category' => 'required|in:must,may',
        ]);

        $user = Auth::user();
        $projectIds = $user->projects()->pluck('id');

        foreach ($request->tasks as $taskData) {
            $task = Task::find($taskData['id']);

            if (!$task) continue;

            // Verify ownership - either user owns the task or the task belongs to their project
            $isOwned = ($task->user_id === $user->id) ||
                       ($task->project_id && in_array($task->project_id, $projectIds->toArray()));

            if (!$isOwned) continue;

            $task->update([
                'order' => $taskData['order'],
                'category' => $taskData['category'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Move a subtask from Recommended to Must Do / May Do.
     * Keeps project_id and parent_id so it can be moved back.
     */
    public function moveSubtaskToStandalone(Request $request, Task $subtask)
    {
        $user = Auth::user();

        // Verify ownership
        if ($subtask->project_id) {
            $project = Project::find($subtask->project_id);
            if (!$project || $project->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($subtask->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category' => 'required|in:must,may',
        ]);

        // Moving to Must Do or May Do
        $maxOrder = Task::where('user_id', $user->id)
            ->whereIn('category', ['must', 'may'])
            ->where('category', $request->category)
            ->max('order') ?? -1;

        $subtask->update([
            'user_id' => $user->id,
            'category' => $request->category,
            'order' => $maxOrder + 1,
            'scheduled_date' => now()->toDateString(), // Tasks only show on the day they're moved
        ]);

        // Return updated recommended list (only tasks still in recommended category)
        $projectIds = $user->projects()->pluck('id');
        $recommended = Task::whereIn('project_id', $projectIds)
            ->whereNotNull('parent_id')
            ->where('completed', false)
            ->where(function ($query) {
                $query->where('category', 'recommended')
                    ->orWhereNull('category');
            })
            ->orderByRaw("CASE
                WHEN due_date IS NOT NULL AND due_date <= date('now', '+7 days')
                THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priority
                WHEN 'high' THEN 1
                WHEN 'med' THEN 2
                WHEN 'low' THEN 3 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->with(['parent:id,title', 'project:id,name'])
            ->limit(3)
            ->get();

        return response()->json([
            'task' => $subtask->fresh(),
            'recommended' => $recommended,
        ]);
    }
}
