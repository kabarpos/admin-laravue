<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { MoreHorizontal, Plus, Trash2, Edit, Eye } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Referensi izin yang sedang diproses
const processingPermission = ref(null);
// State untuk menampilkan loading
const loading = ref(false);

// State untuk dialog konfirmasi
const selectedPermission = ref(null);
const showDeleteDialog = ref(false);

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
];

// Data izin sample untuk tampilan awal
const permissions = ref([
  { 
    id: 1, 
    name: 'view users',
    roles_count: 2,
    created_at: '2023-01-01'
  },
  { 
    id: 2, 
    name: 'create users',
    roles_count: 1,
    created_at: '2023-01-02'
  },
  { 
    id: 3, 
    name: 'edit users',
    roles_count: 1,
    created_at: '2023-01-03'
  },
  { 
    id: 4, 
    name: 'delete users',
    roles_count: 1,
    created_at: '2023-01-04'
  },
  { 
    id: 5, 
    name: 'approve users',
    roles_count: 1,
    created_at: '2023-01-05'
  },
  { 
    id: 6, 
    name: 'view roles',
    roles_count: 2,
    created_at: '2023-01-06'
  },
  { 
    id: 7, 
    name: 'create roles',
    roles_count: 1,
    created_at: '2023-01-07'
  }
]);

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

// Fungsi untuk menampilkan dialog hapus
const showHapusDialog = (permission) => {
  selectedPermission.value = permission;
  showDeleteDialog.value = true;
};

// Fungsi untuk menghapus izin
const hapusPermission = () => {
  if (!selectedPermission.value) return;
  
  loading.value = true;
  processingPermission.value = selectedPermission.value.id;
  
  // Simulasi penghapusan (akan diganti dengan panggilan API)
  setTimeout(() => {
    permissions.value = permissions.value.filter(permission => permission.id !== selectedPermission.value.id);
    
    toast.success('Berhasil', {
      description: `Izin ${selectedPermission.value.name} berhasil dihapus`,
    });
    
    showDeleteDialog.value = false;
    loading.value = false;
    processingPermission.value = null;
  }, 1000);
};
</script>

<template>
  <Head title="Manajemen Izin" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Izin</h1>
        <Link :href="route('admin.permissions.create')" class="cursor-pointer">
          <Button class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <Plus class="h-4 w-4" />
            Tambah Izin
          </Button>
        </Link>
      </div>

      <div class="bg-card text-card-foreground rounded-xl shadow border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <div class="p-6">
          <h2 class="text-lg font-medium">Daftar Izin</h2>
          <p class="text-muted-foreground mt-1">Kelola izin yang akan diberikan kepada peran.</p>
        </div>
        
        <div class="border-t overflow-x-auto">
          <Table class="w-full">
            <TableHeader>
              <TableRow class="hover:bg-transparent border-b border-border">
                <TableHead class="py-3 px-6 font-medium text-muted-foreground">Nama</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground">Digunakan Oleh</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground hidden md:table-cell">Tanggal Dibuat</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground w-[60px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="permission in permissions" :key="permission.id" class="border-b border-border/60 hover:bg-muted/20">
                <TableCell class="py-3.5 px-6 align-middle font-medium">{{ permission.name }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Badge class="text-xs px-2.5 py-0.5">
                    {{ permission.roles_count }} peran
                  </Badge>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-sm text-muted-foreground">{{ formatDate(permission.created_at) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon" class="h-8 w-8 cursor-pointer">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-[160px]">
                      <Link :href="route('admin.permissions.show', permission.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Eye class="h-4 w-4" />
                          <span>Lihat Detail</span>
                        </DropdownMenuItem>
                      </Link>
                      <Link :href="route('admin.permissions.edit', permission.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                      </Link>
                      <DropdownMenuItem 
                        class="flex items-center gap-2 cursor-pointer py-1.5 text-red-600" 
                        :disabled="permission.roles_count > 0"
                        @click="showHapusDialog(permission)"
                      >
                        <Trash2 class="h-4 w-4" />
                        <span>{{ loading && processingPermission === permission.id ? 'Memproses...' : 'Hapus' }}</span>
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
    </div>

    <!-- Dialog Konfirmasi Hapus -->
    <ConfirmationDialog
      :open="showDeleteDialog"
      @update:open="showDeleteDialog = $event"
      title="Konfirmasi Penghapusan"
      dangerMode
      :icon="Trash2"
      :loading="loading"
      confirmLabel="Hapus"
      @confirm="hapusPermission()"
    >
      <p class="mb-2">PERHATIAN: Tindakan ini tidak dapat dibatalkan!</p>
      <p>Apakah Anda yakin ingin menghapus izin <span class="font-semibold">{{ selectedPermission?.name }}</span>?</p>
    </ConfirmationDialog>
  </AppLayout>
</template> 