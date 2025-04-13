<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ArrowLeft } from 'lucide-vue-next';

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
        title: 'Tambah Peran',
        href: '#',
    },
];

// Definisi props dari controller
const props = defineProps<{
    permissions: Array<{
        id: number;
        name: string;
    }>;
}>();

// Form untuk menambah peran
const form = useForm({
    name: '',
    permissions: [] as number[],
});

// State untuk mengelola permission yang dipilih
const isPermissionSelected = (permissionId: number) => {
    return form.permissions.includes(permissionId);
};

// Toggle permission selection
const togglePermission = (permissionId: number) => {
    const index = form.permissions.indexOf(permissionId);
    if (index === -1) {
        form.permissions.push(permissionId);
    } else {
        form.permissions.splice(index, 1);
    }
};

// Submit form
const submit = () => {
    form.post(route('admin.roles.store'), {
        onSuccess: () => {
            // Form akan di-reset otomatis setelah berhasil
        },
    });
};
</script>

<template>
    <Head title="Tambah Peran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.roles.index')">
                    <Button 
                        variant="outline" 
                        size="icon" 
                        class="h-7 w-7 cursor-pointer"
                        type="button"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Tambah Peran</h1>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Informasi Peran -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Peran</CardTitle>
                            <CardDescription>Masukkan informasi dasar peran</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Nama Peran</Label>
                                <Input 
                                    id="name" 
                                    v-model="form.name" 
                                    type="text" 
                                    placeholder="Masukkan nama peran" 
                                    :error="form.errors.name"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <Alert class="mt-4 bg-blue-50 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                <AlertDescription>
                                    Peran merupakan kumpulan izin yang dapat diberikan kepada pengguna.
                                    Contohnya: Admin, User, Moderator, dsb.
                                </AlertDescription>
                            </Alert>
                        </CardContent>
                    </Card>

                    <!-- Izin -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Izin</CardTitle>
                            <CardDescription>Pilih izin yang dimiliki oleh peran ini</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <Alert v-if="props.permissions.length === 0" class="bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                    <AlertDescription>
                                        Tidak ada izin yang tersedia. Tambahkan izin terlebih dahulu.
                                    </AlertDescription>
                                </Alert>

                                <div v-else class="grid grid-cols-1 gap-2 max-h-[400px] overflow-y-auto pr-2">
                                    <div 
                                        v-for="permission in props.permissions" 
                                        :key="permission.id" 
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox 
                                            :id="`permission-${permission.id}`" 
                                            :checked="isPermissionSelected(permission.id)"
                                            @update:checked="togglePermission(permission.id)"
                                        />
                                        <Label :for="`permission-${permission.id}`">{{ permission.name }}</Label>
                                    </div>
                                </div>
                                <p v-if="form.errors.permissions" class="text-sm text-red-500">{{ form.errors.permissions }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-6 flex items-center justify-end gap-4">
                    <Link :href="route('admin.roles.index')">
                        <Button 
                            type="button" 
                            variant="outline"
                            class="cursor-pointer"
                        >
                            Batal
                        </Button>
                    </Link>
                    <Button 
                        type="submit" 
                        :disabled="form.processing"
                    >
                        Simpan
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 