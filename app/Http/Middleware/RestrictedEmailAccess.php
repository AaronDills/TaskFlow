<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictedEmailAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $allowedEmails = config('feedback.allowed_emails', []);

        if (!in_array($user->email, $allowedEmails)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
