<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, PencilLine, ShieldCheck, Calendar, Users } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Izin',
        href: route('admin.permissions.index'),
    },
    {
        title: 'Detail Izin',
        href: '#',
    },
];

// Props dari controller
const props = defineProps<{
    permission: {
        id: number;
        name: string;
        guard_name: string;
        created_at: string;
        updated_at: string;
        roles?: Array<{
            id: number;
            name: string;
        }>;
    };
}>();

// Format tanggal
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Mengecek apakah peran digunakan oleh peran sistem (admin)
const hasSystemRole = () => {
    if (!props.permission.roles) return false;
    return props.permission.roles.some(role => role.name === 'admin' || role.name === 'super-admin');
};

// Memecah nama izin menjadi kategori dan tindakan
const parsePermission = (name: string) => {
    const parts = name.split(' ');
    
    if (parts.length === 1) {
        return {
            category: 'other',
            action: parts[0]
        };
    }
    
    const possibleVerbs = ['view', 'create', 'edit', 'update', 'delete', 'manage'];
    
    let category = parts[0];
    let action = parts.slice(1).join(' ');
    
    // Coba deteksi apakah format adalah "verb noun" atau "noun verb"
    if (possibleVerbs.includes(parts[0])) {
        category = parts.slice(1).join(' ');
        action = parts[0];
    }
    
    return {
        category,
        action
    };
};

const permissionInfo = parsePermission(props.permission.name);
</script>

<template>
    <Head title="Detail Izin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.permissions.index')">
                    <Button 
                        variant="outline" 
                        size="icon" 
                        class="h-7 w-7 cursor-pointer"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Detail Izin</h1>
            </div>

            <div class="flex justify-end gap-3 mb-4">
                <Link 
                    v-if="!hasSystemRole()" 
                    :href="route('admin.permissions.edit', props.permission.id)"
                >
                    <Button 
                        variant="outline"
                        class="cursor-pointer"
                    >
                        <PencilLine class="h-4 w-4 mr-2" />
                        Edit Izin
                    </Button>
                </Link>
                <Button 
                    v-else
                    variant="outline"
                    class="cursor-pointer"
                    disabled
                >
                    <PencilLine class="h-4 w-4 mr-2" />
                    Edit Izin
                </Button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informasi Dasar -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Dasar</CardTitle>
                        <CardDescription>Detail izin</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <ShieldCheck class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nama Izin</p>
                                    <p class="font-medium">{{ props.permission.name }}</p>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        <Badge variant="outline" class="capitalize">
                                            Kategori: {{ permissionInfo.category }}
                                        </Badge>
                                        <Badge variant="outline" class="capitalize">
                                            Tindakan: {{ permissionInfo.action }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <Calendar class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Informasi Waktu</p>
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-1.5">
                                        <div>
                                            <p class="text-xs text-gray-500">Dibuat</p>
                                            <p class="text-sm">{{ formatDate(props.permission.created_at) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Diperbarui</p>
                                            <p class="text-sm">{{ formatDate(props.permission.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Peran yang Menggunakan -->
                <Card v-if="props.permission.roles">
                    <CardHeader>
                        <CardTitle>Peran yang Menggunakan</CardTitle>
                        <CardDescription>
                            Daftar peran yang menggunakan izin ini
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!props.permission.roles || props.permission.roles.length === 0" class="py-2">
                            <p class="text-gray-500 dark:text-gray-400">Izin ini tidak digunakan oleh peran apapun.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div class="flex items-start gap-3">
                                <Users class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Digunakan oleh {{ props.permission.roles.length }} peran</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <Badge 
                                            v-for="role in props.permission.roles" 
                                            :key="role.id"
                                            variant="outline" 
                                            class="capitalize"
                                            :class="{'border-primary text-primary': role.name === 'admin'}"
                                        >
                                            {{ role.name }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 