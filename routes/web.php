<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\FeatureRequestController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/guide', function () {
    return view('guide');
})->name('guide');

Route::get('/todo', function () {
    $projects = Auth::user()->projects()->latest()->get();
    return view('todo', compact('projects'));
})->middleware(['auth', 'verified'])->name('todo');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    // Redirect old show route to unified tasks page
    Route::get('/projects/{project}', function ($project) {
        return redirect()->route('projects.tasks', $project);
    })->name('projects.show');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::put('/projects/{project}/label', [ProjectController::class, 'updateLabel'])->name('projects.label');

    // Labels management
    Route::get('/labels', [LabelController::class, 'index'])->name('labels.index');
    Route::post('/labels', [LabelController::class, 'store'])->name('labels.store');
    Route::put('/labels/{label}', [LabelController::class, 'update'])->name('labels.update');
    Route::delete('/labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy');
    Route::get('/labels/{label}/usage', [LabelController::class, 'usage'])->name('labels.usage');
    
    // Unified task routes (handles both scheduled and unscheduled tasks)
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/projects/{project}/tasks/delete-by-datetime', [TaskController::class, 'deleteByDateTime'])->name('tasks.delete-by-datetime');
    
    // Backward compatibility routes (redirect time-slot routes to task routes)
    Route::get('/projects/{project}/time-slots', [TaskController::class, 'index'])->name('time-slots.index');
    Route::post('/projects/{project}/time-slots', [TaskController::class, 'store'])->name('time-slots.store');
    Route::post('/projects/{project}/time-slots/delete', [TaskController::class, 'deleteByDateTime'])->name('time-slots.delete-by-datetime');

    // Task Management (parent tasks and subtasks)
    Route::get('/projects/{project}/manage-tasks', [ScheduleController::class, 'index'])->name('projects.tasks');
    Route::post('/projects/{project}/parent-tasks', [ScheduleController::class, 'storeParentTask'])->name('parent-tasks.store');
    Route::put('/projects/{project}/parent-tasks/{task}', [ScheduleController::class, 'updateParentTask'])->name('parent-tasks.update');
    Route::put('/projects/{project}/parent-tasks/{task}/toggle-hold', [ScheduleController::class, 'toggleOnHold'])->name('parent-tasks.toggle-hold');
    Route::delete('/projects/{project}/parent-tasks/{task}', [ScheduleController::class, 'destroyParentTask'])->name('parent-tasks.destroy');
    Route::post('/projects/{project}/parent-tasks/{parentTask}/subtasks', [ScheduleController::class, 'storeSubtask'])->name('subtasks.store');
    Route::put('/projects/{project}/subtasks/{subtask}', [ScheduleController::class, 'updateSubtask'])->name('subtasks.update');
    Route::delete('/projects/{project}/subtasks/{subtask}', [ScheduleController::class, 'destroySubtask'])->name('subtasks.destroy');
    Route::get('/projects/{project}/subtasks/recommended', [ScheduleController::class, 'recommended'])->name('subtasks.recommended');
    Route::put('/projects/{project}/subtasks/{subtask}/complete', [ScheduleController::class, 'complete'])->name('subtasks.complete');

    // Global schedule routes (standalone tasks and global recommended)
    Route::get('/schedule/recommended', [ScheduleController::class, 'globalRecommended'])->name('schedule.recommended');
    Route::get('/schedule/tasks', [ScheduleController::class, 'getStandaloneTasks'])->name('schedule.tasks');
    Route::post('/schedule/tasks', [ScheduleController::class, 'storeStandaloneTask'])->name('schedule.tasks.store');
    Route::put('/schedule/tasks/{task}', [ScheduleController::class, 'updateStandaloneTask'])->name('schedule.tasks.update');
    Route::delete('/schedule/tasks/{task}', [ScheduleController::class, 'destroyStandaloneTask'])->name('schedule.tasks.destroy');
    Route::post('/schedule/tasks/reorder', [ScheduleController::class, 'reorderTasks'])->name('schedule.tasks.reorder');
    Route::put('/schedule/subtasks/{subtask}/complete', [ScheduleController::class, 'completeGlobalSubtask'])->name('schedule.subtasks.complete');
    Route::put('/schedule/subtasks/{subtask}/move', [ScheduleController::class, 'moveSubtaskToStandalone'])->name('schedule.subtasks.move');

    // Calendar and events
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar');
    Route::get('/events', [EventController::class, 'getEvents'])->name('events.index');
    Route::get('/events/date', [EventController::class, 'getEventsForDate'])->name('events.date');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/week', [HistoryController::class, 'getWeekData'])->name('history.week');

    // Feedback & Feature Requests (restricted to specific emails)
    Route::middleware('restricted.email')->group(function () {
        Route::get('/feedback', [FeatureRequestController::class, 'index'])->name('feedback.index');
        Route::post('/feedback', [FeatureRequestController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/{featureRequest}', [FeatureRequestController::class, 'show'])->name('feedback.show');
        Route::delete('/feedback/{featureRequest}', [FeatureRequestController::class, 'destroy'])->name('feedback.destroy');
    });
});

require __DIR__.'/auth.php';
