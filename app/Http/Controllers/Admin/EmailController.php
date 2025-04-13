<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EmailController extends Controller
{
    /**
     * Show the form for editing the email settings.
     */
    public function index()
    {
        // Ambil pengaturan email atau buat instance baru jika belum ada
        $emailSettings = EmailSetting::getSettings();
        
        return Inertia::render('admin/Email/Index', [
            'emailSettings' => $emailSettings,
            'title' => 'Pengaturan Email',
        ]);
    }

    /**
     * Update the email settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string|in:smtp,mailgun,ses,log',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'enable_verification' => 'boolean',
            'verification_template' => 'required_if:enable_verification,true|nullable|string',
        ]);

        // Perbarui atau buat pengaturan email
        EmailSetting::updateOrCreate(
            ['id' => 1],
            $validated
        );

        return redirect()->back()
            ->with('message', 'Pengaturan email berhasil diperbarui');
    }

    /**
     * Send a test email using the current email settings.
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        $emailSettings = EmailSetting::getSettings();

        try {
            Mail::to($validated['test_email'])->send(new TestEmail($emailSettings));
            return response()->json(['message' => 'Email uji berhasil dikirim']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim email uji: ' . $e->getMessage()], 500);
        }
    }
} 