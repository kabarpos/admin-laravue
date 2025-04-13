<?php

namespace App\Http\Middleware;

use App\Models\WebsiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleWebsiteSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengambil pengaturan website
        $settings = WebsiteSetting::getSettings();

        // Mengatur judul aplikasi dari pengaturan website
        if ($settings->site_name) {
            config(['app.name' => $settings->site_name]);
        }

        // Meneruskan request
        $response = $next($request);

        // Jika response adalah tipe HTML dan bukan request AJAX
        if ($response instanceof Response 
            && !$request->ajax() 
            && !$request->wantsJson()
            && str_contains($response->headers->get('Content-Type') ?? '', 'text/html')
        ) {
            $content = $response->getContent();

            // Menambahkan favicon jika ada
            if ($settings->favicon_path) {
                $faviconUrl = $settings->getFaviconUrl();
                $favicon = "<link rel=\"icon\" href=\"{$faviconUrl}\" />";
                $content = str_replace('</head>', $favicon . "\n</head>", $content);
            }
            
            // Menambahkan meta untuk SEO jika ada
            if ($settings->site_description) {
                $metaDescription = '<meta name="description" content="' . e($settings->site_description) . '">';
                $content = str_replace('</head>', $metaDescription . "\n</head>", $content);
            }

            // Menambahkan Open Graph metadata
            $ogTags = '';
            if ($settings->site_name) {
                $ogTags .= '<meta property="og:site_name" content="' . e($settings->site_name) . '">' . "\n";
            }
            if ($settings->site_description) {
                $ogTags .= '<meta property="og:description" content="' . e($settings->site_description) . '">' . "\n";
            }
            if ($settings->default_og_image_path) {
                $ogTags .= '<meta property="og:image" content="' . $settings->getOgImageUrl() . '">' . "\n";
            }
            if ($ogTags) {
                $content = str_replace('</head>', $ogTags . '</head>', $content);
            }

            // Menambahkan header scripts (meta pixel, google tag, tiktok pixel, dan script lain)
            $headerScripts = '';
            if ($settings->meta_pixel_script) {
                $headerScripts .= $settings->meta_pixel_script . "\n";
            }
            if ($settings->google_tag_script) {
                $headerScripts .= $settings->google_tag_script . "\n";
            }
            if ($settings->tiktok_pixel_script) {
                $headerScripts .= $settings->tiktok_pixel_script . "\n";
            }
            if ($settings->header_scripts) {
                $headerScripts .= $settings->header_scripts . "\n";
            }
            if ($headerScripts) {
                $content = str_replace('</head>', $headerScripts . '</head>', $content);
            }

            // Menambahkan footer scripts
            if ($settings->footer_scripts) {
                $content = str_replace('</body>', $settings->footer_scripts . "\n</body>", $content);
            }

            $response->setContent($content);
        }

        return $response;
    }
} 