<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { MoreHorizontal, Plus, Trash2, Edit, Key, Eye } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Referensi peran yang sedang diproses
const processingRole = ref(null);
// State untuk menampilkan loading
const loading = ref(false);

// State untuk dialog konfirmasi
const selectedRole = ref(null);
const showDeleteDialog = ref(false);

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
];

// Definisi props dari controller
const props = defineProps<{
    roles: {
        data: Array<{
            id: number;
            name: string;
            permissions: Array<{
                id: number;
                name: string;
            }>;
            users_count?: number;
            created_at: string;
        }>;
        meta?: {
            total?: number;
            current_page?: number;
            last_page?: number;
        };
        links?: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}>();

// Filter data peran untuk menghilangkan peran editor dari tampilan
const filteredRoles = computed(() => {
  return props.roles.data.filter(role => role.name !== 'editor');
});

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

// Fungsi untuk menampilkan dialog hapus
const showHapusDialog = (role) => {
  selectedRole.value = role;
  showDeleteDialog.value = true;
};

// Fungsi untuk menghapus peran
const hapusRole = () => {
  if (!selectedRole.value) return;
  
  loading.value = true;
  processingRole.value = selectedRole.value.id;
  
  router.delete(route('admin.roles.destroy', selectedRole.value.id), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: `Peran ${selectedRole.value.name} berhasil dihapus`,
      });
      showDeleteDialog.value = false;
    },
    onError: (errors) => {
      toast.error('Gagal', {
        description: `Terjadi kesalahan saat menghapus peran: ${errors.message || 'Unknown error'}`,
      });
      console.error('Error:', errors);
    },
    onFinish: () => {
      loading.value = false;
      processingRole.value = null;
    }
  });
};
</script>

<template>
  <Head title="Manajemen Peran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Peran</h1>
        <Link :href="route('admin.roles.create')" class="cursor-pointer">
          <Button class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <Plus class="h-4 w-4" />
            Tambah Peran
          </Button>
        </Link>
      </div>

      <div class="bg-card text-card-foreground rounded-xl shadow border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <div class="p-6">
          <h2 class="text-lg font-medium">Daftar Peran</h2>
          <p class="text-muted-foreground mt-1">Kelola peran dan izin yang terkait dengannya.</p>
        </div>
        
        <div class="border-t overflow-x-auto">
          <Table class="w-full">
            <TableHeader>
              <TableRow class="hover:bg-transparent border-b border-border">
                <TableHead class="py-3 px-6 font-medium text-muted-foreground">Nama</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground">Izin</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground hidden md:table-cell">Jumlah Pengguna</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground hidden md:table-cell">Tanggal Dibuat</TableHead>
                <TableHead class="py-3 px-6 font-medium text-muted-foreground w-[60px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="role in filteredRoles" :key="role.id" class="border-b border-border/60 hover:bg-muted/20">
                <TableCell class="py-3.5 px-6 align-middle font-medium capitalize">{{ role.name }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex gap-1.5 flex-wrap">
                    <Badge variant="outline" class="text-xs px-2 py-0.5">
                      {{ role.permissions.length }} izin
                    </Badge>
                    <Link v-if="role.permissions.length > 0" :href="route('admin.roles.show', role.id)" class="md:hidden">
                      <Badge variant="outline" class="text-xs px-2 py-0.5">
                        <span class="text-xs text-muted-foreground hover:text-foreground cursor-pointer" title="Lihat semua izin">
                          Lihat detail
                        </span>
                      </Badge>
                    </Link>
                    <div class="hidden md:flex md:gap-1.5 md:flex-wrap">
                      <Badge v-if="role.permissions.length > 3" variant="outline" class="text-xs px-2 py-0.5 bg-muted">
                        +{{ role.permissions.length }} izin
                      </Badge>
                      <Badge v-else v-for="permission in role.permissions.slice(0, 3)" :key="permission.id" variant="outline" class="text-xs px-2 py-0.5">
                        {{ permission.name }}
                      </Badge>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-center text-sm text-muted-foreground">
                  {{ role.users_count || 0 }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-sm text-muted-foreground">{{ formatDate(role.created_at) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon" class="h-8 w-8 cursor-pointer">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-[160px]">
                      <Link :href="route('admin.roles.show', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Eye class="h-4 w-4" />
                          <span>Lihat Detail</span>
                        </DropdownMenuItem>
                      </Link>
                      <Link :href="route('admin.roles.edit', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                      </Link>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5" :disabled="role.name === 'admin'">
                        <Key class="h-4 w-4" />
                        <span>Kelola Izin</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem 
                        class="flex items-center gap-2 cursor-pointer py-1.5 text-red-600" 
                        :disabled="role.name === 'admin'"
                        @click="showHapusDialog(role)"
                      >
                        <Trash2 class="h-4 w-4" />
                        <span>{{ loading && processingRole === role.id ? 'Memproses...' : 'Hapus' }}</span>
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination -->
        <div v-if="filteredRoles.length > 0" class="py-4 px-6 flex items-center justify-between border-t">
          <div class="text-sm text-muted-foreground">
            <span v-if="props.roles.meta && props.roles.meta.total">
              Menampilkan {{ filteredRoles.length }} dari {{ props.roles.meta.total - 1 }} peran
            </span>
            <span v-else>
              Menampilkan {{ filteredRoles.length }} peran
            </span>
          </div>
          <div v-if="props.roles.links && props.roles.links.length > 2" class="flex gap-2">
            <Link 
              v-for="(link, i) in props.roles.links.slice(1, -1)" 
              :key="i"
              :href="link.url"
              class="px-3 py-1 rounded text-sm border border-input"
              :class="{ 
                'bg-primary text-primary-foreground border-primary': link.active,
                'cursor-pointer hover:bg-accent': !link.active && link.url
              }"
              v-html="link.label"
            />
          </div>
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
      @confirm="hapusRole()"
    >
      <p class="mb-2">PERHATIAN: Tindakan ini tidak dapat dibatalkan!</p>
      <p>Apakah Anda yakin ingin menghapus peran <span class="font-semibold capitalize">{{ selectedRole?.name }}</span>?</p>
    </ConfirmationDialog>
  </AppLayout>
</template> 