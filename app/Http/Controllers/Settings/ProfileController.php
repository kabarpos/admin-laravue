<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Handle avatar upload jika ada
        if ($request->hasFile('avatar')) {
            // Hapus file avatar lama jika ada
            if ($user->profile_photo_path) {
                $oldPath = storage_path('app/public/' . $user->profile_photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Upload avatar baru
            $path = $request->file('avatar')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Delete the user's profile photo.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Hapus file avatar jika ada
        if ($user->profile_photo_path) {
            $path = storage_path('app/public/' . $user->profile_photo_path);
            if (file_exists($path)) {
                unlink($path);
            }
            
            // Kosongkan path photo di database
            $user->profile_photo_path = null;
            $user->save();
        }
        
        return to_route('profile.edit');
    }
}
