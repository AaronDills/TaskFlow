<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Store a new feedback submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:500',
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'user_email' => $validated['email'],
            'message' => $validated['message'],
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully!',
            'feedback' => $feedback,
        ], 201);
    }
}
