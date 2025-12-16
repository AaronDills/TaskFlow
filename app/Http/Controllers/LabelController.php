<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    /**
     * Get all labels for the authenticated user.
     */
    public function index()
    {
        $labels = Auth::user()->labels()
            ->withCount('projects')
            ->orderBy('name')
            ->get();

        return response()->json($labels);
    }

    /**
     * Store a new label.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|in:' . implode(',', Label::COLORS),
        ]);

        $label = Auth::user()->labels()->create([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return response()->json($label, 201);
    }

    /**
     * Update a label.
     */
    public function update(Request $request, Label $label)
    {
        if ($label->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|in:' . implode(',', Label::COLORS),
        ]);

        $label->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return response()->json($label);
    }

    /**
     * Delete a label.
     */
    public function destroy(Label $label)
    {
        if ($label->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get count of affected projects for response
        $affectedProjects = $label->projects()->count();

        // Delete the label (projects will have label_id set to null due to FK constraint)
        $label->delete();

        return response()->json([
            'success' => true,
            'affected_projects' => $affectedProjects,
        ]);
    }

    /**
     * Get label usage info (for delete confirmation).
     */
    public function usage(Label $label)
    {
        if ($label->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $projects = $label->projects()->select('id', 'name', 'hash')->get();

        return response()->json([
            'label' => $label,
            'projects' => $projects,
            'count' => $projects->count(),
        ]);
    }
}
