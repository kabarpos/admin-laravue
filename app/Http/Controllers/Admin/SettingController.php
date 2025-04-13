<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan website.
     */
    public function index()
    {
        $settings = WebsiteSetting::getSettings();
        
        return Inertia::render('admin/Settings/Index', [
            'settings' => $settings,
            'mediaUrls' => [
                'logo' => $settings->getLogoUrl(),
                'favicon' => $settings->getFaviconUrl(),
                'og_image' => $settings->getOgImageUrl(),
            ],
            'title' => 'Pengaturan Website',
        ]);
    }

    /**
     * Menyimpan pengaturan umum website.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_subtitle' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:255',
            'homepage_route' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan umum berhasil disimpan');
    }

    /**
     * Menyimpan pengaturan footer website.
     */
    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'copyright_text' => 'nullable|string|max:255',
            'copyright_year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan footer berhasil disimpan');
    }

    /**
     * Menyimpan pengaturan skrip tracking.
     */
    public function updateScripts(Request $request)
    {
        $validated = $request->validate([
            'header_scripts' => 'nullable|string',
            'meta_pixel_script' => 'nullable|string',
            'tiktok_pixel_script' => 'nullable|string',
            'google_tag_script' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan skrip berhasil disimpan');
    }

    /**
     * Upload dan simpan logo website.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus logo lama jika ada
        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }
        
        // Simpan logo baru
        $logoPath = $request->file('logo')->store('website', 'public');
        $settings->update(['logo_path' => $logoPath]);

        return redirect()->back()
            ->with('message', 'Logo berhasil diupload');
    }

    /**
     * Upload dan simpan favicon website.
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:ico,png|max:1024',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus favicon lama jika ada
        if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
            Storage::disk('public')->delete($settings->favicon_path);
        }
        
        // Simpan favicon baru
        $faviconPath = $request->file('favicon')->store('website', 'public');
        $settings->update(['favicon_path' => $faviconPath]);

        return redirect()->back()
            ->with('message', 'Favicon berhasil diupload');
    }

    /**
     * Upload dan simpan gambar OG default.
     */
    public function uploadOgImage(Request $request)
    {
        $request->validate([
            'og_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus gambar OG lama jika ada
        if ($settings->default_og_image_path && Storage::disk('public')->exists($settings->default_og_image_path)) {
            Storage::disk('public')->delete($settings->default_og_image_path);
        }
        
        // Simpan gambar OG baru
        $ogImagePath = $request->file('og_image')->store('website', 'public');
        $settings->update(['default_og_image_path' => $ogImagePath]);

        return redirect()->back()
            ->with('message', 'Gambar OG berhasil diupload');
    }
}
