<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'enable_verification',
        'verification_template',
    ];

    protected $casts = [
        'enable_verification' => 'boolean',
    ];

    /**
     * Get default instance or create if not exists
     *
     * @return \App\Models\EmailSetting
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_password' => config('mail.mailers.smtp.password'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
                'enable_verification' => true,
            ]);
        }
        
        return $settings;
    }
}
