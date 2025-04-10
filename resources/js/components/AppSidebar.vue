<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, UsersIcon, ShieldIcon, KeyIcon } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    }
];

// Menu untuk administrasi
const adminNavItems: NavItem[] = [
    {
        title: 'Manajemen Pengguna',
        href: route('admin.users.index'),
        icon: UsersIcon,
    },
    {
        title: 'Manajemen Peran',
        href: route('admin.roles.index'),
        icon: ShieldIcon,
    },
    {
        title: 'Manajemen Izin',
        href: route('admin.permissions.index'),
        icon: KeyIcon,
    }
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <h2 class="text-xs uppercase font-medium text-muted-foreground tracking-wider px-3 mb-2 ml-2">Utama</h2>
            <NavMain :items="mainNavItems" />
            
            <!-- Menu administrasi -->
            <h2 class="text-xs uppercase font-medium text-muted-foreground tracking-wider px-3 mb-2 mt-6 ml-2">Administrasi</h2>
            <NavMain :items="adminNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
