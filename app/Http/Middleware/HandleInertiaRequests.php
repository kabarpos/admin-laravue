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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        
        // Ambil pengaturan website
        $settings = WebsiteSetting::getSettings();
        
        return [
            ...parent::share($request),
            'name' => $settings->site_name ?: config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'websiteSettings' => [
                'siteName' => $settings->site_name,
                'siteSubtitle' => $settings->site_subtitle,
                'siteDescription' => $settings->site_description,
                'contactEmail' => $settings->contact_email,
                'copyright' => $settings->getFullCopyrightText(),
                'logoUrl' => $settings->getLogoUrl(),
                'faviconUrl' => $settings->getFaviconUrl(),
                'ogImageUrl' => $settings->getOgImageUrl(),
            ],
        ];
    }
}
