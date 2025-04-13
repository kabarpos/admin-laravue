import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    requiresRole?: string;
    requiresPermission?: string;
}

export interface WebsiteSettings {
    siteName?: string;
    siteSubtitle?: string;
    siteDescription?: string;
    contactEmail?: string;
    copyright?: string;
    logoUrl?: string | null;
    faviconUrl?: string | null;
    ogImageUrl?: string | null;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    websiteSettings: WebsiteSettings;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at?: string;
    updated_at?: string;
    roles?: { id: number; name: string }[];
    permissions?: string[];
    profile_photo_path?: string | null;
    is_active?: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
