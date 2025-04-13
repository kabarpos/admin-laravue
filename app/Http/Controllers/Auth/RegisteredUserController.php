<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomVerifyEmail;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'whatsapp' => 'nullable|string|max:20|regex:/^\+?[0-9\s\-\(\)]+$/',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
            'status' => 'inactive',
        ]);

        // Cari role 'user' dan berikan ke user baru
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        event(new Registered($user));

        // Periksa apakah verifikasi email diaktifkan
        $emailSettings = EmailSetting::getSettings();
        if ($emailSettings->enable_verification) {
            // Buat URL verifikasi
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
            );

            // Kirim email verifikasi
            Mail::to($user->email)
                ->send(new CustomVerifyEmail($user, $verificationUrl));

            return redirect()->route('login')
                ->with('status', 'Pendaftaran berhasil! Silakan verifikasi email Anda sebelum login.');
        }

        // Tidak melakukan login otomatis, karena perlu persetujuan admin
        // Auth::login($user);

        return redirect()->route('login')
            ->with('status', 'Pendaftaran berhasil! Silakan tunggu persetujuan admin untuk dapat login.');
    }
}
