/**
 * Composable untuk mengelola judul halaman - versi sederhana
 * 
 * CATATAN: Implementasi utama judul halaman sekarang sudah ditangani oleh
 * Inertia title handler di app.ts. Composable ini hanya digunakan
 * untuk kasus-kasus khusus seperti update dinamis judul halaman.
 */

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type PageProps = {
    websiteSettings?: {
        site_name?: string;
        siteName?: string;
    },
    name?: string;
}

export function usePageTitle() {
    const page = usePage<PageProps>();
    
    // Mengambil nama situs dari pengaturan website
    const siteName = computed(() => {
        return page.props.websiteSettings?.siteName 
            || page.props.websiteSettings?.site_name
            || page.props.name
            || import.meta.env.VITE_APP_NAME 
            || 'Laravel';
    });

    /**
     * Mendapatkan judul halaman lengkap dengan nama situs
     * Hanya untuk update dinamis, seperti setelah interaksi pengguna
     */
    function setDocumentTitle(title: string): void {
        // Jika tidak ada title, gunakan nama situs saja
        if (!title) {
            document.title = siteName.value;
            return;
        }
        
        // Jika title sudah mengandung nama situs, gunakan apa adanya
        if (title.includes(siteName.value)) {
            document.title = title;
            return;
        }
        
        // Format: Title - SiteName
        document.title = `${title} - ${siteName.value}`;
    }

    return {
        siteName,
        setDocumentTitle
    };
} 