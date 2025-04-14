<?php

namespace App\Http\Middleware;

use App\Models\WebsiteSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * Konstanta untuk pengaturan default
     */
    const DEFAULT_SETTINGS = [
        'site_name' => null, // Akan menggunakan config('app.name')
        'site_subtitle' => '',
        'site_description' => '',
        'contact_email' => null, // Akan menggunakan config('mail.from.address')
        'copyright' => null, // Akan menggunakan date('Y') + app.name
        'logo_url' => null,
        'favicon_url' => null,
        'og_image_url' => null,
    ];

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
        // Regenerate CSRF token jika melakukan perubahan state (POST, PUT, DELETE)
        $isStateChangingRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
        $csrfToken = $request->session()->token();
        
        if ($isStateChangingRequest && !$request->isXmlHttpRequest() && !$request->ajax()) {
            // Regenerate CSRF token untuk permintaan yang mengubah state tetapi bukan AJAX/XHR
            $request->session()->regenerateToken();
            $csrfToken = $request->session()->token();
        }
        
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
            // Log error untuk debugging
            Log::error('Error saat mengambil pengaturan website', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback jika terjadi error
            $websiteSettings = [
                'site_name' => config('app.name'),
                'site_subtitle' => self::DEFAULT_SETTINGS['site_subtitle'],
                'site_description' => self::DEFAULT_SETTINGS['site_description'],
                'contact_email' => config('mail.from.address', ''),
                'copyright' => date('Y') . ' © ' . config('app.name'),
                'logo_url' => self::DEFAULT_SETTINGS['logo_url'],
                'favicon_url' => self::DEFAULT_SETTINGS['favicon_url'],
                'og_image_url' => self::DEFAULT_SETTINGS['og_image_url'],
                
                // Format camelCase
                'siteName' => config('app.name'),
                'siteSubtitle' => self::DEFAULT_SETTINGS['site_subtitle'],
                'siteDescription' => self::DEFAULT_SETTINGS['site_description'],
                'contactEmail' => config('mail.from.address', ''),
                'logoUrl' => self::DEFAULT_SETTINGS['logo_url'],
                'faviconUrl' => self::DEFAULT_SETTINGS['favicon_url'],
                'ogImageUrl' => self::DEFAULT_SETTINGS['og_image_url'],
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
            // Log error untuk debugging
            Log::warning('Error saat mengambil quote', [
                'message' => $e->getMessage()
            ]);
        }
        
        // Tambahkan component path sebagai meta untuk keperluan title
        $component = $this->getComponentPath($request);
        
        // Prepare data user untuk dibagikan
        $userData = null;
        if ($request->user()) {
            try {
                $userData = array_merge(
                    $request->user()->only(
                        'id', 'name', 'email', 'email_verified_at', 'profile_photo_path', 'is_active'
                    ),
                    [
                        // Tambahkan fields yang mungkin digunakan oleh komponen lama
                        'avatar' => $request->user()->profile_photo_path,
                        
                        // Tambahkan roles dan permissions
                        'roles' => $request->user()->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        }),
                        'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name')->toArray()
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Error saat menyiapkan data user', [
                    'message' => $e->getMessage(),
                    'user_id' => $request->user()->id,
                ]);
                
                // Fallback minimal untuk mencegah error
                $userData = $request->user()->only('id', 'name', 'email');
            }
        }
        
        return array_merge(parent::share($request), [
            'csrf_token' => $csrfToken,
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
                'user' => fn () => $userData,
            ],
            'sidebarOpen' => $this->getSidebarState($request),
            'websiteSettings' => $websiteSettings,
            'quotes' => $quote,
            'componentPath' => $component,
        ]);
    }
    
    /**
     * Get sidebar state from cookie with proper default
     */
    protected function getSidebarState(Request $request): bool
    {
        $cookieValue = $request->cookie('sidebar_state');
        
        // Jika cookie tidak ada, sidebar terbuka
        if ($cookieValue === null) {
            return true;
        }
        
        // Jika cookie ada, konversi nilai ke boolean
        return filter_var($cookieValue, FILTER_VALIDATE_BOOLEAN);
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
            $route = $request->route();
            if (!$route) {
                return null;
            }
            
            $routeName = $route->getName();
            if (!$routeName) {
                return null;
            }
            
            // Gunakan pendekatan dinamis untuk mapping rute ke component
            return $this->mapRouteToComponent($routeName);
            
        } catch (\Exception $e) {
            // Log error dan lanjutkan
            Log::warning('Error saat ekstraksi path komponen', [
                'message' => $e->getMessage(),
                'route' => $request->path()
            ]);
        }
        
        return null;
    }
    
    /**
     * Map a route name to a component path using a dynamic approach
     */
    protected function mapRouteToComponent(string $routeName): ?string
    {
        // Hardcoded mapping untuk backward compatibility
        $routeToComponentMap = [
            'admin.dashboard' => 'admin/Dashboard/Index',
            'admin.users.index' => 'admin/Users/Index',
            'admin.roles.index' => 'admin/Roles/Index',
            'admin.permissions.index' => 'admin/Permissions/Index',
            'admin.email.index' => 'admin/Email/Index',
            'admin.settings.index' => 'admin/Settings/Index',
        ];
        
        // Jika ada di mapping langsung, gunakan itu
        if (isset($routeToComponentMap[$routeName])) {
            return $routeToComponentMap[$routeName];
        }
        
        // Jika tidak, coba generate secara dinamis
        $parts = explode('.', $routeName);
        
        // Jika struktur route tidak sesuai format (minimal namespace.resource[.action])
        if (count($parts) < 2) {
            return null;
        }
        
        // Ambil namespace dan resource
        $namespace = ucfirst($parts[0]);
        $resource = ucfirst($parts[1]);
        
        // Untuk action yang umum (index, create, edit, show)
        if (count($parts) > 2) {
            $action = ucfirst($parts[2]);
            
            // Deteksi pattern yang umum
            if (in_array($action, ['Index', 'Create', 'Edit', 'Show'])) {
                return "{$namespace}/{$resource}/{$action}";
            }
        } else {
            // Default ke Index jika hanya nama.resource (tanpa action)
            return "{$namespace}/{$resource}/Index";
        }
        
        return null;
    }
}
