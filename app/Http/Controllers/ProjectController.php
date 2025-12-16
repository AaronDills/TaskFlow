<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Auth::user()->projects()->with('label')->withCount('tasks')->latest()->get();

        $labels = Auth::user()->labels()->withCount('projects')->orderBy('name')->get();

        // Return JSON for AJAX requests
        if ($request->wantsJson()) {
            return response()->json($projects);
        }

        return view('projects.index', compact('projects', 'labels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Auth::user()->projects()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource - redirects to unified tasks page.
     * Note: The redirect is handled at the route level.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:ready_to_begin,in_progress,on_hold,done',
        ]);

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        $project->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'project' => $project->fresh(),
                'statusLabel' => $project->status_label,
                'statusColor' => $project->status_color,
            ]);
        }

        return redirect()->route('projects.tasks', $project)->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the project (this will cascade delete tasks due to foreign key constraints)
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully']);
    }

    /**
     * Update the label for a project.
     */
    public function updateLabel(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'label_id' => 'nullable|exists:labels,id',
        ]);

        // If label_id is provided, verify ownership
        if ($request->label_id) {
            $label = Label::find($request->label_id);
            if (!$label || $label->user_id !== Auth::id()) {
                return response()->json(['error' => 'Invalid label'], 400);
            }
        }

        $project->update(['label_id' => $request->label_id]);

        return response()->json([
            'success' => true,
            'project' => $project->load('label'),
        ]);
    }
}
