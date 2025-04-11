<?php

namespace App\Mail;

use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * User model
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Verification URL
     *
     * @var string
     */
    protected $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $settings = EmailSetting::getSettings();
        
        if ($settings && $settings->verification_template) {
            // Gunakan template kustom dari pengaturan
            $content = $settings->verification_template;
            $content = str_replace('{name}', $this->user->name, $content);
            $content = str_replace('{verification_url}', $this->verificationUrl, $content);
            
            return new Content(
                htmlString: $content,
            );
        }
        
        // Fallback ke template default
        return new Content(
            markdown: 'emails.verify-email',
            with: [
                'user' => $this->user,
                'url' => $this->verificationUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
