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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-vue-next';
import { onMounted, nextTick } from 'vue';

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
        title: 'Edit Pengguna',
        href: '#',
    },
];

// Definisi props dari controller
const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        status: string;
        email_verified_at: string | null;
    };
    roles: Array<{
        id: number;
        name: string;
    }>;
    userRoles: number[];
}>();

// Form untuk edit pengguna
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '', // Password kosong secara default, opsional saat update
    password_confirmation: '',
    status: props.user.status,
    role_ids: Array.isArray(props.userRoles) ? [...props.userRoles] : [],
    _method: 'PUT',
});

// Inisialisasi role IDs dari props untuk checkbox
console.log('User roles dari props:', props.userRoles);

// Cek apakah sebuah role dipilih
const isRoleSelected = (roleId: number) => {
    return form.role_ids.includes(Number(roleId));
};

// Toggle role selection
const toggleRole = (roleId: number) => {
    roleId = Number(roleId);
    const index = form.role_ids.indexOf(roleId);
    
    if (index === -1) {
        form.role_ids.push(roleId);
    } else {
        form.role_ids.splice(index, 1);
    }
    console.log('Role IDs setelah toggle:', form.role_ids);
};

// Submit form
const submit = () => {
    // Tidak perlu manipulasi password di frontend
    // Form akan mengirim password kosong jika tidak diisi
    // Backend akan memeriksa dengan $request->filled('password')
    form.post(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('User berhasil diperbarui');
            // Reset password fields after successful submission
            form.password = '';
            form.password_confirmation = '';
        },
        onError: (errors) => {
            console.error('Error saat update user:', errors);
        }
    });
};

// Fungsi untuk memastikan password kosong
const forceEmptyPassword = () => {
    // Secara paksa kosongkan password setelah autofill browser
    setTimeout(() => {
        form.password = '';
        form.password_confirmation = '';
    }, 100);
};

// Status options
const statusOptions = [
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Tidak Aktif' },
    { value: 'blocked', label: 'Diblokir' },
];

// Debug dalam lifecycle hook
onMounted(() => {
    console.log('Component mounted');
    console.log('User Data:', props.user);
    console.log('User Roles dari props:', props.userRoles);
    console.log('Form Role IDs awal:', form.role_ids);
    
    // Kosongkan password yang mungkin diisi otomatis browser
    nextTick(() => {
        forceEmptyPassword();
    });
});
</script>

<template>
    <Head title="Edit Pengguna" />

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
                <h1 class="text-2xl font-bold">Edit Pengguna</h1>
            </div>

            <form @submit.prevent="submit" @focusin="forceEmptyPassword">
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Informasi Pengguna -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pengguna</CardTitle>
                            <CardDescription>Ubah informasi dasar pengguna</CardDescription>
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

                            <div class="flex items-center gap-2">
                                <Switch
                                    :id="'verification'"
                                    :disabled="!!props.user.email_verified_at"
                                    :checked="!!props.user.email_verified_at"
                                />
                                <Label for="verification" class="cursor-pointer">
                                    {{ props.user.email_verified_at ? 'Email Terverifikasi' : 'Email Belum Terverifikasi' }}
                                </Label>
                            </div>

                            <div class="pt-4 border-t">
                                <p class="text-sm font-medium mb-2">Ganti Kata Sandi (opsional)</p>
                                <div class="space-y-2 mb-2">
                                    <p class="text-xs text-gray-500 font-medium">
                                        Kosongkan kolom password jika tidak ingin mengubahnya
                                    </p>
                                </div>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="password">Kata Sandi</Label>
                                        <Input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="w-full"
                                            placeholder="Biarkan kosong jika tidak ingin mengubah"
                                            autocomplete="new-password"
                                            @focus="forceEmptyPassword"
                                        />
                                        <p class="text-sm text-muted-foreground">
                                            Biarkan kosong jika tidak ingin mengubah kata sandi
                                        </p>
                                        <div v-if="form.errors.password" class="mt-1 text-red-500 text-sm">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                                        <Input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="w-full"
                                            placeholder="Konfirmasi kata sandi baru"
                                            autocomplete="new-password"
                                            @focus="forceEmptyPassword"
                                        />
                                        <p class="text-sm text-muted-foreground">
                                            Konfirmasi kata sandi baru jika diubah
                                        </p>
                                        <div v-if="form.errors.password_confirmation" class="mt-1 text-red-500 text-sm">
                                            {{ form.errors.password_confirmation }}
                                        </div>
                                    </div>
                                </div>
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
                            <CardDescription>Ubah peran yang dimiliki oleh pengguna</CardDescription>
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
                                        <input 
                                            type="checkbox" 
                                            :id="`role-${role.id}`" 
                                            :value="role.id"
                                            v-model="form.role_ids"
                                            class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary"
                                        />
                                        <Label :for="`role-${role.id}`" class="capitalize">
                                            {{ role.name }}
                                        </Label>
                                    </div>
                                </div>
                                <p v-if="form.errors.role_ids" class="text-sm text-red-500">{{ form.errors.role_ids }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-6 flex items-center justify-end gap-4">
                    <Link :href="route('admin.users.index')">
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
                        class="cursor-pointer"
                    >
                        Simpan Perubahan
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 