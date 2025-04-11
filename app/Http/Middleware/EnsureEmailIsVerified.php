<?php

namespace App\Http\Middleware;

use App\Models\EmailSetting;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah fitur verifikasi email diaktifkan
        $emailSettings = EmailSetting::getSettings();
        
        // Jika verifikasi email dinonaktifkan, lewati pengecekan
        if (!$emailSettings->enable_verification) {
            return $next($request);
        }
        
        // Jika pengguna belum login atau email sudah diverifikasi, lanjutkan
        if (!$request->user() ||
            !($request->user() instanceof MustVerifyEmail) ||
            $request->user()->hasVerifiedEmail()) {
            return $next($request);
        }
        
        // Jika ini adalah request AJAX, kirim respons error
        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'message' => 'Email Anda belum diverifikasi.'
            ], 403);
        }
        
        // Redirect ke halaman notice verifikasi email
        return Redirect::route('verification.notice');
    }
} 