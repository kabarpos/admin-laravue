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
            
            // Ubah title dengan format: [title-halaman] - [title website]
            if ($settings->site_name) {
                // Tambahkan script untuk menangani title dengan akurat
                $script = "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const DEBUG = true; // Set ke false untuk menonaktifkan debugging
                        const siteName = '" . e($settings->site_name) . "';
                        
                        // Fungsi debug untuk konsistensi
                        function log(...args) {
                            if (DEBUG) console.log('[Title Debug]', ...args);
                        }
                        
                        // Fungsi untuk memperbarui judul
                        function updateTitle() {
                            // Debug informasi yang tersedia
                            log('Update title called');
                            if (window.__page) {
                                log('Page component:', window.__page.component);
                                log('Page props:', window.__page.props);
                            } else {
                                log('No __page object available');
                            }
                            
                            let pageTitle = '';
                            
                            // 1. Coba ambil dari properti title langsung
                            if (window.__page?.props?.title) {
                                pageTitle = window.__page.props.title;
                                log('Title from props.title:', pageTitle);
                            }
                            
                            // 2. Coba ambil dari component head props jika tersedia
                            else if (window.__page?.component) {
                                try {
                                    const components = window.__page.component.split('::')[0].split('/');
                                    log('Component parts:', components);
                                    
                                    const lastComponent = components[components.length - 1];
                                    if (lastComponent && lastComponent !== 'Index') {
                                        pageTitle = lastComponent;
                                        log('Title from last component:', pageTitle);
                                    } else if (components.length > 1) {
                                        pageTitle = components[components.length - 2];
                                        log('Title from parent component:', pageTitle);
                                    }
                                } catch (e) {
                                    log('Error extracting component name:', e);
                                }
                            }
                            
                            // 3. Fallback ke metadatanya properti meta
                            if (!pageTitle && window.__page?.props?.meta?.title) {
                                pageTitle = window.__page.props.meta.title;
                                log('Title from props.meta.title:', pageTitle);
                            }
                            
                            // 4. Fallback ke properti view lalu head di component
                            if (!pageTitle && window.__page?.props?.component?.view?.head?.title) {
                                pageTitle = window.__page.props.component.view.head.title;
                                log('Title from component view head:', pageTitle);
                            }
                            
                            // Format judul akhir
                            if (pageTitle) {
                                document.title = pageTitle + ' - ' + siteName;
                                log('Final title:', document.title);
                            } else {
                                document.title = siteName;
                                log('Using only site name:', document.title);
                            }
                        }
                        
                        // Pertama dipanggil saat DOMContentLoaded
                        log('Initial call on DOMContentLoaded');
                        updateTitle();
                        
                        // Kemudian saat setiap navigasi Inertia
                        document.addEventListener('inertia:start', function(event) {
                            log('inertia:start event', event);
                        });
                        
                        document.addEventListener('inertia:finish', function(event) {
                            log('inertia:finish event', event);
                            // Tunggu sebentar agar Inertia sempat memproses head
                            setTimeout(updateTitle, 10);
                        });
                    });
                </script>";
                
                $content = str_replace('</head>', $script . "\n</head>", $content);
            }

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