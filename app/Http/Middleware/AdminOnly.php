<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('custom')->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Check user groups for superuser role (group ID 8)
        $isAdmin = $user->groups()->where('id', 8)->exists();

        if (!$isAdmin) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
