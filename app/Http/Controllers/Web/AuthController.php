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

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('front.auth.register');
    }

    /**
     * Handle a registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:150|unique:vjprf_users,username',
            'email' => 'required|string|email|max:100|unique:vjprf_users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::driver('md5')->make($request->password),
            'registerDate' => now(),
            'lastvisitDate' => now(),
            'block' => 0,
        ]);

        Auth::guard('custom')->login($user);

        $request->session()->regenerate();

        return redirect($request->input('redirect', '/'));
    }

    /**
     * Show the password reset request form.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('front.auth.passwords.email');
    }

    /**
     * Send password reset link to user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // TODO: Implement password reset email sending
        return back()->with('status', 'Password reset link has been sent to your email address.');
    }

    /**
     * Show the password reset form.
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showResetForm($token)
    {
        return view('front.auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Reset the user password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // TODO: Implement password reset logic
        return redirect()->route('auth.login')->with('status', 'Your password has been reset!');
    }
}
