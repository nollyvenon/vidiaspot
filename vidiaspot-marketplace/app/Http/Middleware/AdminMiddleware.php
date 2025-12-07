<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Check if user has admin role or is super admin based on email or role
        if (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com')) {
            return response()->json(['error' => 'Forbidden - Admin access required'], 403);
        }

        return $next($request);
    }
}
