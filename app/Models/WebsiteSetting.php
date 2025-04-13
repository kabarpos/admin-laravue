<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'site_subtitle',
        'site_description',
        'homepage_route',
        'contact_email',
        'copyright_text',
        'copyright_year',
        'logo_path',
        'favicon_path',
        'default_og_image_path',
        'header_scripts',
        'meta_pixel_script',
        'tiktok_pixel_script',
        'google_tag_script',
        'footer_scripts',
    ];

    /**
     * Mendapatkan pengaturan website.
     * Jika belum ada, buat instance default.
     *
     * @return \App\Models\WebsiteSetting
     */
    public static function getSettings()
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'site_name' => config('app.name', 'Laravel'),
                'copyright_year' => date('Y'),
                'homepage_route' => '/',
            ]);
        }

        return $settings;
    }

    /**
     * Mendapatkan URL lengkap untuk aset gambar.
     *
     * @param string $path
     * @return string|null
     */
    public function getMediaUrl(string $path = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Jika sudah berupa URL lengkap
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Jika file disimpan secara lokal
        return asset('storage/' . $path);
    }

    /**
     * Mendapatkan URL logo.
     *
     * @return string|null
     */
    public function getLogoUrl(): ?string
    {
        return $this->getMediaUrl($this->logo_path);
    }

    /**
     * Mendapatkan URL favicon.
     *
     * @return string|null
     */
    public function getFaviconUrl(): ?string
    {
        return $this->getMediaUrl($this->favicon_path);
    }

    /**
     * Mendapatkan URL gambar OG default.
     *
     * @return string|null
     */
    public function getOgImageUrl(): ?string
    {
        return $this->getMediaUrl($this->default_og_image_path);
    }

    /**
     * Mendapatkan teks copyright yang lengkap.
     *
     * @return string
     */
    public function getFullCopyrightText(): string
    {
        $year = $this->copyright_year ?: date('Y');
        $text = $this->copyright_text ?: $this->site_name ?: config('app.name', 'Laravel');
        
        return "© {$year} {$text}";
    }
}
