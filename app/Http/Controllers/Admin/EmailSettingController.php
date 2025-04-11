<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EmailSettingController extends Controller
{
    /**
     * Menampilkan form pengaturan email
     *
     * @return \Inertia\Response
     */
    public function edit()
    {
        // Ambil pengaturan email atau buat instance baru jika belum ada
        $emailSettings = EmailSetting::getSettings();
        
        return Inertia::render('admin/Settings/EmailSettings', [
            'emailSettings' => $emailSettings,
        ]);
    }
    
    /**
     * Update pengaturan email
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'enable_verification' => 'boolean',
            'verification_template' => 'nullable|string',
        ]);
        
        // Ambil pengaturan email atau buat instance baru
        $emailSettings = EmailSetting::getSettings();
        $emailSettings->fill($validated);
        $emailSettings->save();
        
        // Update konfigurasi email secara real-time untuk session saat ini
        config([
            'mail.default' => $emailSettings->mail_driver,
            'mail.mailers.smtp.host' => $emailSettings->mail_host,
            'mail.mailers.smtp.port' => $emailSettings->mail_port,
            'mail.mailers.smtp.username' => $emailSettings->mail_username,
            'mail.mailers.smtp.password' => $emailSettings->mail_password,
            'mail.mailers.smtp.encryption' => $emailSettings->mail_encryption,
            'mail.from.address' => $emailSettings->mail_from_address,
            'mail.from.name' => $emailSettings->mail_from_name,
        ]);
        
        return redirect()->back()->with('success', 'Pengaturan email berhasil disimpan');
    }
    
    /**
     * Kirim email tes
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);
        
        try {
            // Kirim email tes
            Mail::raw('Ini adalah email tes untuk memverifikasi konfigurasi email Anda berjalan dengan baik.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email dari ' . config('app.name'));
            });
            
            return redirect()->back()->with('success', 'Email tes berhasil dikirim');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
