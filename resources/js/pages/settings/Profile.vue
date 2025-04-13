<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onBeforeUnmount } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Upload, User as UserIcon, Trash2 } from 'lucide-vue-next';
import { useInitials } from '@/composables/useInitials';
import { getAvatarUrl, validateAvatarFile, createAvatarPreview } from '@/utils/avatarUtils';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengaturan Profil',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { getInitials } = useInitials();
const errorMessage = ref<string | null>(null);

// Avatar preview URL dan cleanup function
const avatarPreview = ref<string | null>(getAvatarUrl(user.profile_photo_path || null));
const avatarFile = ref<File | null>(null);
const revokePreviewFn = ref<(() => void) | null>(null);

// Cleanup function untuk URL objek saat komponen unmounted
onBeforeUnmount(() => {
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
    }
});

// Get initials for avatar fallback
const userInitials = computed(() => getInitials(user.name));

// Form untuk update profile
const form = useForm({
    name: user.name,
    email: user.email,
    avatar: null as File | null,
    _method: 'PATCH',
});

// Fungsi untuk menangani file upload avatar
const handleAvatarChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (!target.files?.length) return;
    
    const file = target.files[0];
    
    // Reset error message
    errorMessage.value = null;
    
    // Validasi file
    const validation = validateAvatarFile(file);
    if (!validation.valid) {
        errorMessage.value = validation.message || 'File tidak valid';
        target.value = ''; // Reset input file
        return;
    }
    
    // Cleanup preview URL sebelumnya
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
    }
    
    // Buat preview baru
    const { previewUrl, revokePreview } = createAvatarPreview(file);
    avatarPreview.value = previewUrl;
    revokePreviewFn.value = revokePreview;
    
    avatarFile.value = file;
    form.avatar = file;
};

// Fungsi untuk menghapus avatar
const removeAvatar = () => {
    // Cleanup preview URL
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
        revokePreviewFn.value = null;
    }
    
    avatarPreview.value = null;
    avatarFile.value = null;
    form.avatar = null;
    
    // Set ini ke true untuk memberitahu server bahwa kita ingin menghapus avatar
    form._method = 'DELETE';
    form.post(route('profile.avatar.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            form._method = 'PATCH'; // Kembalikan ke PATCH untuk operasi normal
        },
    });
};

// Submit form
const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Pengaturan Profil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <!-- Avatar Upload Section -->
                <Card>
                    <CardHeader>
                        <CardTitle>Foto Profil</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="flex items-center justify-center">
                                <Avatar class="h-24 w-24">
                                    <AvatarImage v-if="avatarPreview" :src="avatarPreview" alt="Avatar" />
                                    <AvatarFallback class="text-lg">{{ userInitials }}</AvatarFallback>
                                </Avatar>
                            </div>
                            
                            <div class="flex flex-col space-y-4">
                                <div>
                                    <Label for="avatar" class="block mb-2">Upload Foto Profil</Label>
                                    <Input 
                                        id="avatar" 
                                        type="file" 
                                        accept="image/*" 
                                        @change="handleAvatarChange" 
                                        :error="!!form.errors.avatar"
                                    />
                                    <p class="text-xs text-muted-foreground mt-1">Format: JPG, PNG, GIF, WEBP. Ukuran maksimal: 2MB.</p>
                                    <InputError class="mt-1" :message="form.errors.avatar" />
                                    <p v-if="errorMessage" class="mt-1 text-sm text-red-600">{{ errorMessage }}</p>
                                </div>
                                
                                <div class="flex gap-2">
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        size="sm" 
                                        @click="removeAvatar"
                                        :disabled="!avatarPreview || form.processing"
                                    >
                                        <Trash2 class="h-4 w-4 mr-1" />
                                        Hapus Foto
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Profile Information -->
                <HeadingSmall title="Informasi Profil" description="Perbarui nama dan alamat email Anda" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Nama</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Nama lengkap" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Alamat Email</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Alamat email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Alamat email Anda belum diverifikasi.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Klik di sini untuk mengirim ulang email verifikasi.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            Link verifikasi baru telah dikirim ke alamat email Anda.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Upload v-if="!form.processing" class="h-4 w-4 mr-2" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Tersimpan.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
