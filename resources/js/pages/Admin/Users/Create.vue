<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

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
        title: 'Tambah Pengguna',
        href: '#',
    },
];

// Definisi props dari controller
const props = defineProps<{
    roles: Array<{
        id: number;
        name: string;
    }>;
}>();

// Form untuk menambah pengguna
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: 'active',
    role_ids: [] as number[],
});

// State untuk mengelola role yang dipilih
const isRoleSelected = (roleId: number) => {
    return form.role_ids.includes(roleId);
};

// Toggle role selection
const toggleRole = (roleId: number) => {
    const index = form.role_ids.indexOf(roleId);
    if (index === -1) {
        form.role_ids.push(roleId);
    } else {
        form.role_ids.splice(index, 1);
    }
};

// Submit form
const submit = () => {
    form.post(route('admin.users.store'), {
        onSuccess: () => {
            // Form akan di-reset otomatis setelah berhasil
        },
    });
};

// Status options
const statusOptions = [
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Tidak Aktif' },
    { value: 'blocked', label: 'Diblokir' },
];
</script>

<template>
    <Head title="Tambah Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Button 
                    variant="outline" 
                    size="icon" 
                    class="h-7 w-7 cursor-pointer"
                    :href="route('admin.users.index')"
                >
                    <ArrowLeft class="h-4 w-4" />
                </Button>
                <h1 class="text-2xl font-bold">Tambah Pengguna</h1>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Informasi Pengguna -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pengguna</CardTitle>
                            <CardDescription>Masukkan informasi dasar pengguna</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Nama</Label>
                                <Input 
                                    id="name" 
                                    v-model="form.name" 
                                    type="text" 
                                    placeholder="Masukkan nama pengguna" 
                                    :error="form.errors.name"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input 
                                    id="email" 
                                    v-model="form.email" 
                                    type="email" 
                                    placeholder="Masukkan email pengguna"
                                    :error="form.errors.email"
                                />
                                <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="password">Kata Sandi</Label>
                                <Input 
                                    id="password" 
                                    v-model="form.password" 
                                    type="password" 
                                    placeholder="Masukkan kata sandi"
                                    :error="form.errors.password"
                                />
                                <p v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                                <Input 
                                    id="password_confirmation" 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    placeholder="Konfirmasi kata sandi"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger :class="{ 'border-red-500': form.errors.status }">
                                        <SelectValue placeholder="Pilih status pengguna" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="option in statusOptions" 
                                            :key="option.value" 
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Peran Pengguna -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Peran Pengguna</CardTitle>
                            <CardDescription>Pilih peran yang dimiliki oleh pengguna</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <Alert v-if="props.roles.length === 0" class="bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                    <AlertDescription>
                                        Tidak ada peran yang tersedia. Tambahkan peran terlebih dahulu.
                                    </AlertDescription>
                                </Alert>

                                <div v-else class="space-y-2">
                                    <div 
                                        v-for="role in props.roles" 
                                        :key="role.id" 
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox 
                                            :id="`role-${role.id}`" 
                                            :checked="isRoleSelected(role.id)"
                                            @update:checked="toggleRole(role.id)"
                                        />
                                        <Label :for="`role-${role.id}`" class="capitalize">{{ role.name }}</Label>
                                    </div>
                                </div>
                                <p v-if="form.errors.role_ids" class="text-sm text-red-500">{{ form.errors.role_ids }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-6 flex items-center justify-end gap-4">
                    <Button 
                        type="button" 
                        variant="outline"
                        class="cursor-pointer"
                        :href="route('admin.users.index')"
                    >
                        Batal
                    </Button>
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