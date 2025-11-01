<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        // Redirect to dashboard if already logged in
        if (Auth::guard('custom')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->input('username'))->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['User not found.'],
            ]);
        }

        if ($user->isBlocked()) {
            throw ValidationException::withMessages([
                'username' => ['This account is blocked.'],
            ]);
        }

        if (!Hash::driver('md5')->check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Invalid password.'],
            ]);
        }

        // Only allow superusers (group ID 8)
        $isAdmin = $user->groups()->where('id', 8)->exists();
        if (!$isAdmin) {
            throw ValidationException::withMessages([
                'username' => ['You do not have admin access.'],
            ]);
        }

        Auth::guard('custom')->login($user);
        $user->update(['lastvisitDate' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('custom')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
