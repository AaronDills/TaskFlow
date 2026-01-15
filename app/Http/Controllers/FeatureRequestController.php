<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFeatureRequest;
use App\Models\FeatureRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureRequestController extends Controller
{
    /**
     * Display the feedback form page.
     */
    public function index()
    {
        $featureRequests = FeatureRequest::forUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('feedback.index', compact('featureRequests'));
    }

    /**
     * Store a new feature request or feedback.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'type' => 'required|in:feedback,feature_request,bug_report',
            'priority' => 'required|in:low,medium,high',
        ]);

        $featureRequest = Auth::user()->featureRequests()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'status' => FeatureRequest::STATUS_PENDING,
        ]);

        // Dispatch job immediately to process the request
        ProcessFeatureRequest::dispatch($featureRequest);

        if ($request->wantsJson()) {
            return response()->json($featureRequest, 201);
        }

        return redirect()->route('feedback.index')
            ->with('success', 'Your feedback has been submitted successfully!');
    }

    /**
     * Display a specific feature request.
     */
    public function show(FeatureRequest $featureRequest)
    {
        if ($featureRequest->user_id !== Auth::id()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        if (request()->wantsJson()) {
            return response()->json($featureRequest);
        }

        return view('feedback.show', compact('featureRequest'));
    }

    /**
     * Delete a feature request (only if pending).
     */
    public function destroy(FeatureRequest $featureRequest)
    {
        if ($featureRequest->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$featureRequest->isPending()) {
            return response()->json([
                'error' => 'Cannot delete a request that is already being processed'
            ], 422);
        }

        $featureRequest->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('feedback.index')
            ->with('success', 'Request deleted successfully.');
    }
}
