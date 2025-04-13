<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { useRoles } from '@/composables/useRoles';
import type { Role, Permission } from '@/types/models';

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
        title: 'Edit Peran',
        href: '#',
    },
];

// Props dari controller
const props = defineProps<{
    role: Role & { permissions: Permission[] };
    permissions: Permission[];
}>();

// State
const roleError = ref('');
const { loading, updateRole } = useRoles();

// Form untuk edit
const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map(p => p.id),
});

// Untuk menampilkan error jika tidak ada permissions yang dipilih
watch(() => form.permissions, (newValue) => {
    if (newValue.length === 0) {
        roleError.value = 'Peran harus memiliki minimal 1 perizinan';
    } else {
        roleError.value = '';
    }
}, { immediate: true });

// Cek apakah permission sudah dipilih
const isPermissionSelected = (permissionId: number) => {
    return form.permissions.includes(Number(permissionId));
};

// Toggle selection untuk permission
const togglePermission = (permissionId: number) => {
    permissionId = Number(permissionId);
    const index = form.permissions.indexOf(permissionId);
    if (index === -1) {
        form.permissions.push(permissionId);
    } else {
        // Jangan izinkan menghapus permission jika hanya tersisa 1
        if (form.permissions.length > 1) {
            form.permissions.splice(index, 1);
        } else {
            // Tampilkan error jika user mencoba menghapus permission terakhir
            roleError.value = 'Peran harus memiliki minimal 1 perizinan';
        }
    }
};

// Submit form
const submit = () => {
    // Validasi sebelum submit
    if (form.permissions.length === 0) {
        roleError.value = 'Peran harus memiliki minimal 1 perizinan';
        return;
    }

    form.put(route('admin.roles.update', props.role.id));
};
</script>

<template>
    <Head title="Edit Peran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Button 
                    variant="outline" 
                    size="icon" 
                    class="h-7 w-7 cursor-pointer"
                    type="button"
                    @click="router.visit(route('admin.roles.index'))"
                >
                    <i class="i-lucide-arrow-left h-4 w-4"></i>
                </Button>
                <h1 class="text-2xl font-bold">Edit Peran</h1>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Informasi Peran -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Peran</CardTitle>
                            <CardDescription>Edit informasi dasar peran</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Nama Peran</Label>
                                <Input 
                                    id="name" 
                                    v-model="form.name" 
                                    type="text" 
                                    placeholder="Masukkan nama peran" 
                                    :disabled="props.role.name === 'admin' || loading"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Perizinan Peran -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Perizinan Peran</CardTitle>
                            <CardDescription>Pilih perizinan yang dimiliki oleh peran ini (wajib minimal 1 perizinan)</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-if="props.permissions.length === 0" class="bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 p-3 rounded-md text-sm">
                                    Tidak ada perizinan yang tersedia. Tambahkan perizinan terlebih dahulu.
                                </div>

                                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div 
                                        v-for="permission in props.permissions" 
                                        :key="permission.id" 
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox 
                                            :id="`permission-${permission.id}`" 
                                            :checked="isPermissionSelected(permission.id)"
                                            @update:checked="togglePermission(permission.id)"
                                            :disabled="loading"
                                        />
                                        <Label :for="`permission-${permission.id}`" class="cursor-pointer">
                                            {{ permission.name }}
                                        </Label>
                                    </div>
                                </div>
                                
                                <!-- Error untuk permissions -->
                                <p v-if="form.errors.permissions" class="text-sm text-red-500">{{ form.errors.permissions }}</p>
                                <p v-if="roleError" class="text-sm text-red-500">{{ roleError }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="router.visit(route('admin.roles.index'))"
                        :disabled="loading"
                    >
                        Batal
                    </Button>
                    <Button 
                        type="submit"
                        :disabled="loading || form.processing || roleError !== ''"
                        class="px-6"
                    >
                        {{ loading || form.processing ? 'Memproses...' : 'Simpan Perubahan' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 