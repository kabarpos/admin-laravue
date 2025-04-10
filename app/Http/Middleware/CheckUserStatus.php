<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user tidak terautentikasi, lanjutkan ke middleware berikutnya
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Jika status user tidak aktif, logout dan redirect ke login dengan pesan error
        if ($user->status !== 'active') {
            $message = match($user->status) {
                'inactive' => 'Akun Anda belum diaktifkan. Silakan tunggu persetujuan admin.',
                'blocked' => 'Akun Anda telah diblokir. Silakan hubungi admin untuk informasi lebih lanjut.',
                'rejected' => 'Akun Anda telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut.',
                default => 'Akun Anda sedang tidak aktif.',
            };

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', $message);
        }

        return $next($request);
    }
}
