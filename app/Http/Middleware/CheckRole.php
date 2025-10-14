<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request for specific roles.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!session('authenticated')) {
            return redirect('/login');
        }

        // Use 'role' from session (matches LoginController)
        $userRole = session('role');

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}