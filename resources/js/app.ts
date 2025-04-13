import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import { Toaster } from 'vue-sonner';

// Deklarasi typing untuk objek __page global yang disisipkan oleh Inertia
declare global {
    interface Window {
        __page?: {
            props?: {
                websiteSettings?: {
                    site_name?: string;
                    siteName?: string;
                    logoUrl?: string;
                    faviconUrl?: string;
                };
                name?: string;
                title?: string;
                componentPath?: string;
            };
            component?: string;
        };
    }
}

// Ambil nama aplikasi dari environment variable
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    // Format judul halaman: [title] - [siteName]
    title: (title) => {
        // Log untuk debugging
        console.log('Title dari Head component:', title);
        console.log('Page props:', window.__page?.props);
        
        // Ambil nama situs dari props Inertia
        const siteName = window.__page?.props?.websiteSettings?.siteName 
            || window.__page?.props?.websiteSettings?.site_name
            || window.__page?.props?.name
            || import.meta.env.VITE_APP_NAME 
            || 'Laravel';
        
        // Coba dapatkan judul dari berbagai sumber
        let pageTitle = title;
        
        // Jika tidak ada title dari Head component, coba ambil dari props
        if (!pageTitle && window.__page?.props?.title) {
            pageTitle = window.__page.props.title;
            console.log('Menggunakan title dari props:', pageTitle);
        }
        
        // Jika masih tidak ada, periksa componentPath dari props
        if (!pageTitle && window.__page?.props?.componentPath) {
            const path = window.__page.props.componentPath;
            console.log('Using componentPath from props:', path);
            const parts = path.split('/');
            if (parts.length > 1) {
                // Pengaturan Email/Index -> Pengaturan Email
                const lastPart = parts[parts.length - 1];
                if (lastPart === 'Index' && parts.length > 2) {
                    pageTitle = parts[parts.length - 2];
                    // Format: Email -> Pengaturan Email
                    if (pageTitle === 'Email') pageTitle = 'Pengaturan Email';
                    else if (pageTitle === 'Roles') pageTitle = 'Manajemen Peran';
                    else if (pageTitle === 'Permissions') pageTitle = 'Manajemen Izin';
                    else if (pageTitle === 'Users') pageTitle = 'Manajemen Pengguna';
                    else if (pageTitle === 'Settings') pageTitle = 'Pengaturan Website';
                } else {
                    pageTitle = lastPart;
                }
                console.log('Extracted title from componentPath:', pageTitle);
            }
        }
        
        // Jika masih tidak ada, coba ekstrak dari komponen path
        if (!pageTitle && window.__page?.component) {
            const parts = window.__page.component.split('/');
            if (parts.length > 1) {
                // Pengaturan Email/Index -> Pengaturan Email
                const lastPart = parts[parts.length - 1];
                if (lastPart === 'Index' && parts.length > 2) {
                    pageTitle = parts[parts.length - 2];
                    // Format: Email -> Pengaturan Email
                    if (pageTitle === 'Email') pageTitle = 'Pengaturan Email';
                    else if (pageTitle === 'Roles') pageTitle = 'Manajemen Peran';
                    else if (pageTitle === 'Permissions') pageTitle = 'Manajemen Izin';
                    else if (pageTitle === 'Users') pageTitle = 'Manajemen Pengguna';
                    else if (pageTitle === 'Settings') pageTitle = 'Pengaturan Website';
                } else {
                    pageTitle = lastPart;
                }
                console.log('Extracted title from component path:', pageTitle);
            }
        }
        
        // Jika masih tidak ada judul, gunakan nama situs
        if (!pageTitle) {
            console.log('Tidak ada title, menggunakan siteName:', siteName);
            return siteName;
        }
        
        // Jika judul sudah mengandung nama situs, gunakan apa adanya
        if (pageTitle.includes(siteName)) {
            console.log('Title sudah mengandung siteName:', pageTitle);
            return pageTitle;
        }
        
        // Format: Title - SiteName
        const finalTitle = `${pageTitle} - ${siteName}`;
        console.log('Final title:', finalTitle);
        return finalTitle;
    },
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin)
           .use(ZiggyVue)
           .component('Toaster', Toaster)
           .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
