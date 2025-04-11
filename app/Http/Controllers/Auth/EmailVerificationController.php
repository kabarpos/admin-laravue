<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomVerifyEmail;
use App\Models\EmailSetting;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationController extends Controller
{
    /**
     * Tampilkan halaman notifikasi verifikasi email
     */
    public function notice(Request $request): Response|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return Inertia::render('auth/VerifyEmail');
    }

    /**
     * Kirim ulang email verifikasi
     */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Periksa pengaturan untuk melihat apakah verifikasi diaktifkan
        $settings = EmailSetting::getSettings();
        if (!$settings->enable_verification) {
            // Jika fitur verifikasi dinonaktifkan, verifikasi pengguna secara otomatis
            $request->user()->markEmailAsVerified();
            return redirect()->intended(route('dashboard'));
        }

        // Buat URL verifikasi
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $request->user()->getKey(), 'hash' => sha1($request->user()->getEmailForVerification())]
        );

        // Kirim email verifikasi kustom
        Mail::to($request->user()->email)
            ->send(new CustomVerifyEmail($request->user(), $verificationUrl));

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Verifikasi email
     */
    public function verify(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard').'?verified=1');
        }

        if ($request->user()?->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard').'?verified=1');
    }
}
