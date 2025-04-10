<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, PencilLine, UserCog, KeyRound, Calendar, Mail, CircleCheck, CircleX } from 'lucide-vue-next';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Pengguna',
        href: route('admin.users.index'),
    },
    {
        title: 'Detail Pengguna',
        href: '#',
    },
];

// Props dari controller
const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        status: string;
        email_verified_at: string | null;
        created_at: string;
        updated_at: string;
        roles: Array<{
            id: number;
            name: string;
        }>;
    };
}>();

// Format tanggal
const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Tidak tersedia';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Get status badge variant
const getStatusBadge = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
        case 'inactive':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
        case 'blocked':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
    }
};

// Get status label
const getStatusLabel = (status: string) => {
    switch (status) {
        case 'active':
            return 'Aktif';
        case 'inactive':
            return 'Tidak Aktif';
        case 'blocked':
            return 'Diblokir';
        default:
            return 'Tidak Diketahui';
    }
};
</script>

<template>
    <Head title="Detail Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.users.index')">
                    <Button 
                        variant="outline" 
                        size="icon" 
                        class="h-7 w-7 cursor-pointer"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Detail Pengguna</h1>
            </div>

            <div class="flex justify-end gap-3 mb-4">
                <Link :href="route('admin.users.edit', props.user.id)">
                    <Button 
                        variant="outline"
                        class="cursor-pointer"
                    >
                        <PencilLine class="h-4 w-4 mr-2" />
                        Edit Pengguna
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informasi Dasar -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Dasar</CardTitle>
                        <CardDescription>Detail data pengguna</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <UserCog class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                    <p class="font-medium">{{ props.user.name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Mail class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="font-medium">{{ props.user.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div v-if="props.user.email_verified_at">
                                    <CircleCheck class="h-5 w-5 mt-0.5 text-green-500" />
                                </div>
                                <div v-else>
                                    <CircleX class="h-5 w-5 mt-0.5 text-amber-500" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status Verifikasi</p>
                                    <p class="font-medium">
                                        {{ props.user.email_verified_at ? 'Email terverifikasi' : 'Email belum terverifikasi' }}
                                    </p>
                                    <p v-if="props.user.email_verified_at" class="text-xs text-gray-500 mt-1">
                                        Terverifikasi pada {{ formatDate(props.user.email_verified_at) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <KeyRound class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                    <Badge :class="getStatusBadge(props.user.status)">
                                        {{ getStatusLabel(props.user.status) }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Calendar class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Informasi Waktu</p>
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-1.5">
                                        <div>
                                            <p class="text-xs text-gray-500">Dibuat</p>
                                            <p class="text-sm">{{ formatDate(props.user.created_at) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Diperbarui</p>
                                            <p class="text-sm">{{ formatDate(props.user.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Peran Pengguna -->
                <Card>
                    <CardHeader>
                        <CardTitle>Peran Pengguna</CardTitle>
                        <CardDescription>Peran dan izin yang dimiliki pengguna</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="props.user.roles.length === 0" class="py-2">
                            <p class="text-gray-500 dark:text-gray-400">Pengguna tidak memiliki peran.</p>
                        </div>

                        <div v-else class="flex flex-wrap gap-2">
                            <Badge 
                                v-for="role in props.user.roles" 
                                :key="role.id"
                                variant="outline" 
                                class="capitalize border-primary text-primary"
                            >
                                {{ role.name }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 