<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, UsersIcon, ShieldIcon, KeyIcon, Settings, Mail } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

// Ambil informasi user dari usePage
const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);

// Cek apakah user memiliki role tertentu
const hasRole = (role: string) => {
    if (!user.value || !user.value.roles) return false;
    return user.value.roles.some(r => r.name === role);
};

// Cek apakah user memiliki permission tertentu
const hasPermission = (permission: string) => {
    if (!user.value || !user.value.permissions) return false;
    return user.value.permissions.includes(permission);
};

// Filter item navigasi berdasarkan persyaratan akses
const filterNavItems = (items: NavItem[]) => {
    return items.filter(item => {
        // Jika tidak ada requirement, semua orang bisa akses
        if (!item.requiresRole && !item.requiresPermission) return true;
        
        // Cek role
        if (item.requiresRole && !hasRole(item.requiresRole)) return false;
        
        // Cek permission
        if (item.requiresPermission && !hasPermission(item.requiresPermission)) return false;
        
        return true;
    });
};

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
        title: 'Dashboard Admin',
        href: route('admin.dashboard'),
        icon: LayoutGrid,
        requiresRole: 'admin',
    },
    {
        title: 'Manajemen Pengguna',
        href: route('admin.users.index'),
        icon: UsersIcon,
        requiresRole: 'admin',
        requiresPermission: 'manage_users',
    },
    {
        title: 'Manajemen Peran',
        href: route('admin.roles.index'),
        icon: ShieldIcon,
        requiresRole: 'admin',
        requiresPermission: 'manage_roles',
    },
    {
        title: 'Manajemen Izin',
        href: route('admin.permissions.index'),
        icon: KeyIcon,
        requiresRole: 'admin',
        requiresPermission: 'manage_permissions',
    },
    {
        title: 'Pengaturan Email',
        href: route('admin.email.index'),
        icon: Mail,
        requiresRole: 'admin',
        requiresPermission: 'manage_settings',
    },
    {
        title: 'Pengaturan Website',
        href: route('admin.settings.index'),
        icon: Settings,
        requiresRole: 'admin',
        requiresPermission: 'manage_settings',
    }
];

// Computed untuk menu yang difilter
const filteredMainNavItems = computed(() => filterNavItems(mainNavItems));
const filteredAdminNavItems = computed(() => filterNavItems(adminNavItems));
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
            <NavMain :items="filteredMainNavItems" />
            
            <!-- Menu administrasi -->
            <NavMain v-if="filteredAdminNavItems.length > 0" :items="filteredAdminNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
