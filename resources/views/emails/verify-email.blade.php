@component('mail::message')
# Verifikasi Email Anda

Hai {{ $user->name }},

Terima kasih telah mendaftar di {{ config('app.name') }}. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.

@component('mail::button', ['url' => $url])
Verifikasi Email
@endcomponent

Jika Anda tidak membuat akun, abaikan email ini.

Salam,<br>
{{ config('app.name') }}
@endcomponent 