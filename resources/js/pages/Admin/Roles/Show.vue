<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, PencilLine, ShieldCheck, Calendar } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Peran',
        href: route('admin.roles.index'),
    },
    {
        title: 'Detail Peran',
        href: '#',
    },
];

// Props dari controller
const props = defineProps<{
    role: {
        id: number;
        name: string;
        guard_name: string;
        created_at: string;
        updated_at: string;
        permissions: Array<{
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

// Mengelompokkan izin berdasarkan kategori
// Asumsi format izin: "kategori tindakan" (contoh: "users view")
const groupedPermissions = () => {
    const groups = {};
    
    props.role.permissions.forEach(permission => {
        const parts = permission.name.split(' ');
        let category = 'other';
        
        // Mencoba mendapatkan kategori dari nama izin
        if (parts.length > 1) {
            const possibleCategories = ['users', 'roles', 'permissions', 'posts', 'comments', 'settings'];
            const foundCategory = parts.find(part => possibleCategories.includes(part));
            if (foundCategory) {
                category = foundCategory;
            }
        }
        
        if (!groups[category]) {
            groups[category] = [];
        }
        
        groups[category].push(permission);
    });
    
    return groups;
};
</script>

<template>
    <Head title="Detail Peran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.roles.index')">
                    <Button 
                        variant="outline" 
                        size="icon" 
                        class="h-7 w-7 cursor-pointer"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Detail Peran</h1>
            </div>

            <div class="flex justify-end gap-3 mb-4">
                <Link 
                    v-if="props.role.name !== 'admin'" 
                    :href="route('admin.roles.edit', props.role.id)"
                >
                    <Button 
                        variant="outline"
                        class="cursor-pointer"
                    >
                        <PencilLine class="h-4 w-4 mr-2" />
                        Edit Peran
                    </Button>
                </Link>
                <Button 
                    v-else
                    variant="outline"
                    class="cursor-pointer"
                    disabled
                >
                    <PencilLine class="h-4 w-4 mr-2" />
                    Edit Peran
                </Button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informasi Dasar -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Dasar</CardTitle>
                        <CardDescription>Detail peran</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <ShieldCheck class="h-5 w-5 mt-0.5 text-gray-500" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nama Peran</p>
                                    <p class="font-medium capitalize">{{ props.role.name }}</p>
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
                                            <p class="text-sm">{{ formatDate(props.role.created_at) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Diperbarui</p>
                                            <p class="text-sm">{{ formatDate(props.role.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Izin -->
                <Card class="md:row-span-2">
                    <CardHeader>
                        <CardTitle>Izin</CardTitle>
                        <CardDescription>
                            Daftar izin yang dimiliki oleh peran ini ({{ props.role.permissions.length }} izin)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="props.role.permissions.length === 0" class="py-2">
                            <p class="text-gray-500 dark:text-gray-400">Peran ini tidak memiliki izin.</p>
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="(permissions, category) in groupedPermissions()" :key="category" class="space-y-2">
                                <h3 class="text-sm font-medium capitalize">{{ category }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    <Badge 
                                        v-for="permission in permissions" 
                                        :key="permission.id"
                                        variant="outline" 
                                        class="whitespace-nowrap"
                                    >
                                        {{ permission.name }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 