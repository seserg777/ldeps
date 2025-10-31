<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form for users.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('front.auth.login');
    }

    /**
     * Handle a login request for users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        // Find user by username only
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['Користувач з таким логіном не знайдений.'],
            ]);
        }

        // Check if user is blocked
        if ($user->isBlocked()) {
            throw ValidationException::withMessages([
                'username' => ['Цей обліковий запис заблокований.'],
            ]);
        }

        // Verify password using MD5
        if (!Hash::driver('md5')->check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Невірний пароль.'],
            ]);
        }

        // Login the user using custom guard
        Auth::guard('custom')->login($user);

        // Update last visit date
        $user->update(['lastvisitDate' => now()]);

        $request->session()->regenerate();

        // Prefer explicit redirect URL from the form (stay on the same page)
        $redirectUrl = $request->input('redirect');
        if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            return redirect()->to($redirectUrl);
        }

        return redirect()->intended(url()->previous() ?: '/');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('custom')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectUrl = $request->input('redirect');
        if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            return redirect()->to($redirectUrl);
        }
        return redirect()->back()->withInput([]);
    }

    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User\User|null
     */
    public function user()
    {
        return Auth::guard('custom')->user();
    }

    /**
     * Check if user is authenticated with custom guard.
     *
     * @return bool
     */
    public function check()
    {
        return Auth::guard('custom')->check();
    }
}
