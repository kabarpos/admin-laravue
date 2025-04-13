<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EmailSettingController extends Controller
{
    /**
     * Show the form for editing the email settings.
     */
    public function edit()
    {
        // Ambil pengaturan email atau buat instance baru jika belum ada
        $emailSettings = EmailSetting::getSettings();
        
        return Inertia::render('admin/Settings/EmailSettings', [
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
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|email|max:255',
            'verification_enabled' => 'boolean',
            'verification_subject' => 'required_if:verification_enabled,true|nullable|string|max:255',
            'verification_template' => 'required_if:verification_enabled,true|nullable|string',
        ]);

        // Perbarui atau buat pengaturan email
        EmailSetting::updateOrCreate(
            ['id' => 1],
            $validated
        );

        return redirect()->route('admin.email-settings.edit')
            ->with('message', 'Pengaturan email berhasil diperbarui');
    }

    /**
     * Send a test email using the current email settings.
     */
    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        $emailSettings = EmailSetting::getSettings();

        try {
            Mail::to($validated['test_email'])->send(new TestEmail($emailSettings));
            return redirect()->route('admin.email-settings.edit')
                ->with('message', 'Email uji berhasil dikirim');
        } catch (\Exception $e) {
            return redirect()->route('admin.email-settings.edit')
                ->with('error', 'Gagal mengirim email uji: ' . $e->getMessage());
        }
    }
}
