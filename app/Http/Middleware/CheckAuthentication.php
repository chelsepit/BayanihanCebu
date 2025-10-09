<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!session('authenticated') || !session('user_id') || !session('remember_token')) {
            return redirect('/login')->with('error', 'Please login to continue.');
        }

        // Verify token in database
        $user = DB::table('users')
            ->where('user_id', session('user_id'))
            ->where('remember_token', session('remember_token'))
            ->first();

        if (!$user) {
            // Token invalid, clear session
            session()->flush();
            return redirect('/login')->with('error', 'Session expired. Please login again.');
        }

        // Check if user has required role
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

        // Refresh session data
        session([
            'user_role' => $user->role,
            'user_name' => $user->full_name,
            'barangay_id' => $user->barangay_id,
        ]);

        return $next($request);
    }
}