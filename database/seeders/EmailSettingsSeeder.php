<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat pengaturan email default
        EmailSetting::create([
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => '2525',
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'admin@example.com',
            'mail_from_name' => 'Admin Laravel',
            'enable_verification' => true,
            'verification_template' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email Anda</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #4f46e5; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; }
        .footer { background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email Anda</h1>
        </div>
        <div class="content">
            <p>Hai {name},</p>
            <p>Terima kasih telah mendaftar di aplikasi kami. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.</p>
            <div style="text-align: center;">
                <a href="{verification_url}" class="button">Verifikasi Email</a>
            </div>
            <p>Jika Anda tidak membuat akun, abaikan email ini.</p>
            <p>Salam,<br>Tim Kami</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>',
        ]);
    }
} 