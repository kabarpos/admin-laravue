<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ArrowLeft } from 'lucide-vue-next';

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
        title: 'Edit Izin',
        href: '#',
    },
];

// Definisi props dari controller
const props = defineProps<{
    permission: {
        id: number;
        name: string;
        guard_name: string;
        created_at: string;
        updated_at: string;
    };
}>();

// Form untuk edit izin
const form = useForm({
    name: props.permission.name,
    _method: 'PUT',
});

// Submit form
const submit = () => {
    form.post(route('admin.permissions.update', props.permission.id), {
        onSuccess: () => {
            // Form akan di-reset otomatis setelah berhasil
        },
    });
};
</script>

<template>
    <Head title="Edit Izin" />

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
                <h1 class="text-2xl font-bold">Edit Izin</h1>
            </div>

            <Alert v-if="permission.name.includes('admin')" class="mb-4 bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                <AlertDescription>
                    Izin ini mungkin merupakan izin sistem yang penting. Modifikasi pada izin ini dapat memengaruhi fungsi sistem secara keseluruhan.
                </AlertDescription>
            </Alert>

            <form @submit.prevent="submit">
                <div class="max-w-2xl mx-auto">
                    <!-- Informasi Izin -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Izin</CardTitle>
                            <CardDescription>Ubah informasi izin</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Nama Izin</Label>
                                <Input 
                                    id="name" 
                                    v-model="form.name" 
                                    type="text" 
                                    placeholder="Contoh: users view, roles create, dsb." 
                                    :error="form.errors.name"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <Alert class="mt-4 bg-blue-50 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                <AlertDescription>
                                    Gunakan format standar untuk nama izin, seperti: "resource action" (contoh: "users view", "posts create").
                                    Format ini mempermudah pengelompokan dan identifikasi izin.
                                </AlertDescription>
                            </Alert>
                        </CardContent>
                    </Card>

                    <div class="mt-6 flex items-center justify-end gap-4">
                        <Link :href="route('admin.permissions.index')">
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
                            Simpan Perubahan
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 