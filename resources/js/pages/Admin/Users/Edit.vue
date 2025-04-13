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
import { ArrowLeft, Mail, CheckCircle } from 'lucide-vue-next';
import { onMounted, nextTick, ref, watch } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';

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
        whatsapp: string | null;
        status: string;
        email_verified_at: string | null;
    };
    roles: Array<{
        id: number;
        name: string;
    }>;
    userRoles: number[];
}>();

// State untuk menyimpan error status role
const roleError = ref('');
const isEmailVerified = ref(!!props.user.email_verified_at);
const isResendingVerification = ref(false);
const isMarkingVerified = ref(false);

// Buat form untuk mengedit data pengguna
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    whatsapp: props.user.whatsapp || '',
    password: '',
    password_confirmation: '',
    status: props.user.status,
    role_ids: props.userRoles.map(id => Number(id)),
});

// Mengirim ulang email verifikasi
const resendVerificationEmail = () => {
    isResendingVerification.value = true;
    axios.post(route('admin.users.resend-verification', props.user.id))
        .then(() => {
            toast.success('Berhasil', {
                description: 'Email verifikasi berhasil dikirim ulang',
            });
        })
        .catch((error) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
            });
        })
        .finally(() => {
            isResendingVerification.value = false;
        });
};

// Menandai email sebagai terverifikasi secara manual
const markEmailAsVerified = () => {
    isMarkingVerified.value = true;
    axios.post(route('admin.users.mark-verified', props.user.id))
        .then(() => {
            toast.success('Berhasil', {
                description: 'Email pengguna telah ditandai sebagai terverifikasi',
            });
            isEmailVerified.value = true;
        })
        .catch((error) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
            });
        })
        .finally(() => {
            isMarkingVerified.value = false;
        });
};

// Watcher untuk memvalidasi setidaknya ada 1 role yang dipilih
watch(() => form.role_ids, (newValue) => {
    if (newValue.length === 0) {
        roleError.value = 'Pengguna harus memiliki minimal 1 peran';
    } else {
        roleError.value = '';
    }
}, { immediate: true });

// Cek apakah sebuah role dipilih
const isRoleSelected = (roleId: number) => {
    roleId = Number(roleId);
    return form.role_ids.includes(roleId);
};

// Toggle role selection
const toggleRole = (roleId: number) => {
    roleId = Number(roleId);
    const index = form.role_ids.indexOf(roleId);
    
    if (index === -1) {
        form.role_ids.push(roleId);
    } else {
        // Jangan izinkan menghapus role jika hanya tersisa 1
        if (form.role_ids.length > 1) {
            form.role_ids.splice(index, 1);
        } else {
            // Jika mencoba menghapus role terakhir, tampilkan pesan error
            roleError.value = 'Pengguna harus memiliki minimal 1 peran';
        }
    }
};

// Submit form
const submit = () => {
    // Validasi minimal 1 role sebelum submit
    if (form.role_ids.length === 0) {
        roleError.value = 'Pengguna harus memiliki minimal 1 peran';
        return;
    }

    // Pastikan semua role_ids adalah number sebelum dikirim
    form.role_ids = form.role_ids.map(id => Number(id));

    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Reset password fields after successful submission
            form.password = '';
            form.password_confirmation = '';
        },
        onError: (errors) => {
            // Biarkan error ditangani oleh mekanisme default inertia
        }
    });
};

// Fungsi untuk memastikan password kosong saat pertama kali komponen dimuat
// untuk mengatasi autofill browser
const forceEmptyPassword = () => {
    // Kosongkan password hanya saat pertama kali komponen dimuat
    form.password = '';
    form.password_confirmation = '';
};

// Status options
const statusOptions = [
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Tidak Aktif' },
    { value: 'blocked', label: 'Diblokir' },
];

// Lifecycle hook
onMounted(() => {
    // Force set data dalam onMounted untuk memastikan data terisi
    if (Array.isArray(props.userRoles) && props.userRoles.length > 0) {
        const roleIdsFromProps = props.userRoles.map(id => Number(id));
        form.role_ids = [...roleIdsFromProps];
    }
    
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

            <form @submit.prevent="submit">
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

                            <div class="space-y-2">
                                <Label for="whatsapp">Nomor WhatsApp</Label>
                                <Input 
                                    id="whatsapp" 
                                    v-model="form.whatsapp" 
                                    type="tel" 
                                    placeholder="Masukkan nomor WhatsApp"
                                    :error="form.errors.whatsapp"
                                />
                                <p v-if="form.errors.whatsapp" class="text-sm text-red-500">{{ form.errors.whatsapp }}</p>
                            </div>

                            <!-- Email Verification Status -->
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-sm font-medium mb-3">Status Verifikasi Email</p>
                                <div class="flex items-center gap-2">
                                    <Switch
                                        :id="'verification'"
                                        :disabled="true"
                                        :checked="isEmailVerified"
                                    />
                                    <Label for="verification" class="cursor-pointer">
                                        {{ isEmailVerified ? 'Email Terverifikasi' : 'Email Belum Terverifikasi' }}
                                    </Label>
                                </div>

                                <div class="flex gap-2 mt-3" v-if="!isEmailVerified">
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        size="sm"
                                        @click="resendVerificationEmail" 
                                        :disabled="isResendingVerification"
                                    >
                                        <Mail class="h-4 w-4 mr-2" />
                                        {{ isResendingVerification ? 'Mengirim...' : 'Kirim Ulang Verifikasi' }}
                                    </Button>

                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        size="sm"
                                        @click="markEmailAsVerified" 
                                        :disabled="isMarkingVerified"
                                    >
                                        <CheckCircle class="h-4 w-4 mr-2" />
                                        {{ isMarkingVerified ? 'Memproses...' : 'Tandai Sebagai Terverifikasi' }}
                                    </Button>
                                </div>
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
                                    <SelectTrigger :class="{ 'border-red-500': form.errors.status, 'cursor-pointer': true }">
                                        <SelectValue placeholder="Pilih status pengguna" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="option in statusOptions" 
                                            :key="option.value" 
                                            :value="option.value"
                                            class="cursor-pointer"
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
                            <CardDescription>
                                Ubah peran yang dimiliki oleh pengguna (wajib minimal 1 peran)
                            </CardDescription>
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
                                            :value="Number(role.id)"
                                            :model-value="isRoleSelected(role.id)"
                                            @update:model-value="(checked) => {
                                                if (checked) {
                                                    if (!form.role_ids.includes(Number(role.id))) {
                                                        form.role_ids.push(Number(role.id));
                                                    }
                                                } else {
                                                    if (form.role_ids.length > 1) {
                                                        const index = form.role_ids.indexOf(Number(role.id));
                                                        if (index !== -1) form.role_ids.splice(index, 1);
                                                    } else {
                                                        roleError = 'Pengguna harus memiliki minimal 1 peran';
                                                    }
                                                }
                                            }"
                                        />
                                        <Label :for="`role-${role.id}`" class="capitalize cursor-pointer">
                                            {{ role.name }}
                                        </Label>
                                    </div>
                                </div>
                                
                                <!-- Error untuk role_ids -->
                                <p v-if="form.errors.role_ids" class="text-sm text-red-500">{{ form.errors.role_ids }}</p>
                                <p v-if="roleError" class="text-sm text-red-500">{{ roleError }}</p>
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