<?php

namespace App\Providers;

use App\Models\EmailSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Hanya jalankan jika tabel email_settings sudah ada dalam database
            if (Schema::hasTable('email_settings')) {
                $settings = EmailSetting::first();
                
                if ($settings) {
                    // Ubah konfigurasi mail secara dinamis
                    config([
                        'mail.default' => $settings->mail_driver,
                        'mail.mailers.smtp.host' => $settings->mail_host,
                        'mail.mailers.smtp.port' => $settings->mail_port,
                        'mail.mailers.smtp.username' => $settings->mail_username,
                        'mail.mailers.smtp.password' => $settings->mail_password,
                        'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                        'mail.from.address' => $settings->mail_from_address ?: config('mail.from.address'),
                        'mail.from.name' => $settings->mail_from_name ?: config('mail.from.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Tangani jika tabel belum dibuat (saat migrasi awal)
            report($e);
        }
    }
}
