<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\User\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::guard('custom')->user();

        // Get profile data
        $profileData = [];
        foreach ($user->profiles as $profile) {
            $profileData[$profile->profile_key] = $profile->profile_value;
        }

        return view('front.profile.index', compact('user', 'profileData'));
    }

    /**
     * Show the profile edit form.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::guard('custom')->user();

        // Get profile data
        $profileData = [];
        foreach ($user->profiles as $profile) {
            $profileData[$profile->profile_key] = $profile->profile_value;
        }

        return view('front.profile.edit', compact('user', 'profileData'));
    }

    /**
     * Update the user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::guard('custom')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('vjprf_users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update basic user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update profile data
        $profileFields = [
            'profile.phone' => $request->phone,
            'profile.company' => $request->company,
            'profile.country' => $request->country,
            'profile.city' => $request->city,
            'profile.address' => $request->address,
        ];

        foreach ($profileFields as $key => $value) {
            if ($value !== null) {
                $user->setProfileValue($key, $value);
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::driver('md5')->make($request->password)
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Профіль успішно оновлено!');
    }
}
