<?php

namespace App\Http\Middleware;

use App\Models\WebsiteSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Ambil pengaturan website - pastikan error handling memadai
        $websiteSettings = null;
        try {
            $settings = WebsiteSetting::getSettings();
            $websiteSettings = [
                // Format kebab-case untuk key lama (backward compatibility)
                'site_name' => $settings->site_name ?: config('app.name'),
                'site_subtitle' => $settings->site_subtitle,
                'site_description' => $settings->site_description,
                'contact_email' => $settings->contact_email,
                'copyright' => $settings->getFullCopyrightText(),
                'logo_url' => $settings->getLogoUrl(),
                'favicon_url' => $settings->getFaviconUrl(),
                'og_image_url' => $settings->getOgImageUrl(),
                
                // Format camelCase untuk key yang digunakan oleh komponen UI
                'siteName' => $settings->site_name ?: config('app.name'),
                'siteSubtitle' => $settings->site_subtitle,
                'siteDescription' => $settings->site_description,
                'contactEmail' => $settings->contact_email,
                'logoUrl' => $settings->getLogoUrl(),
                'faviconUrl' => $settings->getFaviconUrl(),
                'ogImageUrl' => $settings->getOgImageUrl(),
            ];
        } catch (\Exception $e) {
            // Fallback jika terjadi error
            $websiteSettings = [
                'site_name' => config('app.name'),
                'siteName' => config('app.name')
            ];
        }
        
        // Ambil quote inspiratif untuk dashboard
        $quote = [];
        try {
            // Pisahkan quote dan author jika menggunakan format dengan '-'
            $inspireQuote = Inspiring::quote();
            if (str_contains($inspireQuote, '-')) {
                [$message, $author] = str($inspireQuote)->explode('-');
                $quote = [
                    'inspire' => $inspireQuote,
                    'message' => trim($message),
                    'author' => trim($author)
                ];
            } else {
                $quote = ['inspire' => $inspireQuote];
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        // Tambahkan component path sebagai meta untuk keperluan title
        $component = $this->getComponentPath($request);
        
        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'name' => $websiteSettings['siteName'] ?? config('app.name'),  // Untuk compatibility
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user()
                    ? $request->user()->only(
                        'id', 'name', 'email', 'email_verified_at', 'profile_photo_path', 'is_active'
                    )
                    : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'websiteSettings' => $websiteSettings,
            'quotes' => $quote,
            'componentPath' => $component,
        ]);
    }
    
    /**
     * Get the component path from the current request (for title extraction).
     */
    protected function getComponentPath(Request $request): ?string
    {
        // Check if we're processing an Inertia request
        if ($request->header('X-Inertia')) {
            $component = $request->input('component');
            if ($component) {
                return $component;
            }
        }
        
        // Try to extract from the URL/route
        try {
            $route = $request->route()->getName();
            if ($route) {
                // Map route names to component paths
                $routeToComponentMap = [
                    'admin.dashboard' => 'admin/Dashboard/Index',
                    'admin.users.index' => 'admin/Users/Index',
                    'admin.roles.index' => 'admin/Roles/Index',
                    'admin.permissions.index' => 'admin/Permissions/Index',
                    'admin.email.index' => 'admin/Email/Index',
                    'admin.settings.index' => 'admin/Settings/Index',
                ];
                
                return $routeToComponentMap[$route] ?? null;
            }
        } catch (\Exception $e) {
            // Ignore route extraction errors
        }
        
        return null;
    }
}
