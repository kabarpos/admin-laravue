<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        {{-- Default OG tags yang akan di-override oleh middleware --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
        <meta property="twitter:card" content="summary_large_image">
        
        {{-- Title disediakan untuk JavaScript supaya selalu tersedia --}}
        <title data-page="{{ json_encode($page) }}">{{ config('app.name') }}</title>

        {{-- Script untuk mengatur judul halaman secara korektif --}}
        <script>
            // Fungsi untuk mengatur judul halaman
            function updatePageTitle() {
                try {
                    // Ambil data halaman dari window.__page
                    // Atau fallback ke data-page pada tag title
                    const titleElement = document.querySelector('title');
                    let pageData = window.__page;
                    
                    // Jika tidak ada data halaman di window.__page (kemungkinan awal halaman dimuat)
                    if (!pageData && titleElement && titleElement.getAttribute('data-page')) {
                        try {
                            // Parse data dari atribut data-page
                            pageData = JSON.parse(titleElement.getAttribute('data-page'));
                        } catch (e) {
                            console.error('Error parsing page data:', e);
                        }
                    }
                    
                    // Periksa ketersediaan data halaman
                    if (!pageData || !pageData.props) {
                        return;
                    }
                    
                    // Ambil title dari props jika tersedia
                    let pageTitle = pageData.props.title;
                    
                    // Jika tidak ada, ekstrak dari component path
                    if (!pageTitle && pageData.component) {
                        const parts = pageData.component.split('/');
                        if (parts.length > 0) {
                            const lastPart = parts[parts.length - 1];
                            if (lastPart === 'Index' && parts.length > 2) {
                                pageTitle = parts[parts.length - 2];
                                // Format nama halaman sesuai kebutuhan
                                if (pageTitle === 'Email') pageTitle = 'Pengaturan Email';
                                else if (pageTitle === 'Roles') pageTitle = 'Manajemen Peran';
                                else if (pageTitle === 'Permissions') pageTitle = 'Manajemen Izin';
                                else if (pageTitle === 'Users') pageTitle = 'Manajemen Pengguna';
                                else if (pageTitle === 'Settings') pageTitle = 'Pengaturan Website';
                            } else {
                                pageTitle = lastPart;
                            }
                        }
                    }
                    
                    // Ambil nama website
                    const siteName = pageData.props.websiteSettings?.siteName || 
                                    pageData.props.websiteSettings?.site_name || 
                                    pageData.props.name || 
                                    "{{ config('app.name') }}";
                    
                    // Set judul halaman dengan format yang tepat
                    if (pageTitle) {
                        document.title = `${pageTitle} - ${siteName}`;
                    } else {
                        document.title = siteName;
                    }
                } catch(e) {
                    console.error('Error saat memperbarui judul halaman:', e);
                }
            }

            // Panggil updatePageTitle segera untuk halaman pertama
            document.addEventListener('DOMContentLoaded', updatePageTitle);
            
            // Setup untuk Inertia router events
            document.addEventListener('inertia:init', function() {
                // Pastikan window.Inertia tersedia
                if (window.Inertia && window.Inertia.on) {
                    // Dengarkan event finish dari router Inertia
                    window.Inertia.on('navigate', () => {
                        // Gunakan setTimeout untuk memberi waktu window.__page diperbarui
                        setTimeout(updatePageTitle, 0);
                    });
                }
            });
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
