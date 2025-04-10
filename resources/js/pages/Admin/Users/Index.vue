<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Plus, Check, X, Trash2, Edit, Shield, Eye } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Referensi pengguna yang sedang diproses
const processingUser = ref(null);
// State untuk menampilkan loading
const loading = ref(false);

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
];

// Debug routes
onMounted(() => {
  // Debug route name dan path untuk membantu troubleshooting
  try {
    // Contoh user ID untuk debugging
    const sampleUserId = 1;
    console.log('Route update-status path:', route('admin.users.update-status', sampleUserId));
    console.log('Available routes:', route().current());
  } catch(e) {
    console.error('Error saat debugging route:', e);
  }
});

// Definisi props dari controller
const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            status: string;
            created_at: string;
            roles: Array<{
                id: number;
                name: string;
            }>;
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

const getStatusColor = (status) => {
  switch(status) {
    case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'inactive': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    case 'blocked': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    case 'rejected': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    default: return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

// Fungsi untuk mengaktifkan pengguna
const aktivasiUser = (user) => {
  if (confirm(`Apakah Anda yakin ingin mengaktifkan pengguna ${user.name}?`)) {
    loading.value = true;
    processingUser.value = user.id;
    
    console.log('Mengirim request aktivasi ke:', route('admin.users.update-status', user.id));
    
    // Format data sesuai dokumentasi Inertia.js
    router.patch(route('admin.users.update-status', user.id), {
      status: 'active'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Berhasil', {
          description: `Pengguna ${user.name} berhasil diaktifkan`,
        });
      },
      onError: (errors) => {
        toast.error('Gagal', {
          description: `Terjadi kesalahan saat mengaktifkan pengguna: ${errors.message || 'Unknown error'}`,
        });
        console.error('Error saat aktivasi:', errors);
      },
      onFinish: () => {
        loading.value = false;
        processingUser.value = null;
      }
    });
  }
};

// Fungsi untuk memblokir pengguna
const blokirUser = (user) => {
  if (confirm(`Apakah Anda yakin ingin memblokir pengguna ${user.name}? Pengguna tidak akan bisa login.`)) {
    loading.value = true;
    processingUser.value = user.id;
    
    console.log('Mengirim request blokir ke:', route('admin.users.update-status', user.id));
    
    // Format data sesuai dokumentasi Inertia.js
    router.patch(route('admin.users.update-status', user.id), {
      status: 'blocked'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Berhasil', {
          description: `Pengguna ${user.name} berhasil diblokir`,
        });
      },
      onError: (errors) => {
        toast.error('Gagal', {
          description: `Terjadi kesalahan saat memblokir pengguna: ${errors.message || 'Unknown error'}`,
        });
        console.error('Error saat blokir:', errors);
      },
      onFinish: () => {
        loading.value = false;
        processingUser.value = null;
      }
    });
  }
};

// Fungsi untuk menghapus pengguna
const hapusUser = (user) => {
  if (confirm(`PERHATIAN: Tindakan ini tidak dapat dibatalkan!\nApakah Anda yakin ingin menghapus pengguna ${user.name}?`)) {
    loading.value = true;
    processingUser.value = user.id;
    
    router.delete(route('admin.users.destroy', user.id), {
      onSuccess: () => {
        toast.success('Berhasil', {
          description: `Pengguna ${user.name} berhasil dihapus`,
        });
      },
      onError: (errors) => {
        toast.error('Gagal', {
          description: `Terjadi kesalahan saat menghapus pengguna: ${errors.message || 'Unknown error'}`,
        });
        console.error('Error:', errors);
      },
      onFinish: () => {
        loading.value = false;
        processingUser.value = null;
      }
    });
  }
};
</script>

<template>
  <Head title="Manajemen Pengguna" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Pengguna</h1>
        <Link :href="route('admin.users.create')" class="cursor-pointer">
          <Button class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <Plus class="h-4 w-4" />
            Tambah Pengguna
          </Button>
        </Link>
      </div>

      <div class="bg-card text-card-foreground rounded-xl shadow border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <div class="p-6">
          <h2 class="text-lg font-medium">Daftar Pengguna</h2>
          <p class="text-muted-foreground mt-1">Kelola pengguna dan akses mereka di sistem.</p>
        </div>
        
        <div class="border-t overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nama</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Status</TableHead>
                <TableHead class="hidden md:table-cell">Peran</TableHead>
                <TableHead class="hidden md:table-cell">Tanggal Daftar</TableHead>
                <TableHead class="w-[80px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="user in props.users.data" :key="user.id">
                <TableCell class="font-medium">{{ user.name }}</TableCell>
                <TableCell class="break-all">{{ user.email }}</TableCell>
                <TableCell>
                  <Badge :class="getStatusColor(user.status)">
                    {{ user.status === 'active' ? 'Aktif' : 
                       user.status === 'inactive' ? 'Tidak Aktif' : 
                       user.status === 'blocked' ? 'Diblokir' : 'Ditolak' }}
                  </Badge>
                </TableCell>
                <TableCell class="hidden md:table-cell">
                  <div class="flex gap-1 flex-wrap">
                    <Badge v-for="role in user.roles" :key="role.id" variant="outline" class="capitalize">
                      {{ role.name }}
                    </Badge>
                  </div>
                </TableCell>
                <TableCell class="hidden md:table-cell">{{ formatDate(user.created_at) }}</TableCell>
                <TableCell>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon" class="cursor-pointer">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <Link :href="route('admin.users.show', user.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                          <Eye class="h-4 w-4" />
                          <span>Lihat Detail</span>
                        </DropdownMenuItem>
                      </Link>
                      <Link :href="route('admin.users.edit', user.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                      </Link>
                      <DropdownMenuItem 
                        v-if="user.status !== 'active'" 
                        @click="aktivasiUser(user)"
                        class="flex items-center gap-2 cursor-pointer"
                        :disabled="loading && processingUser === user.id"
                      >
                        <Check class="h-4 w-4" />
                        <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Aktifkan' }}</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem 
                        v-if="user.status !== 'blocked'" 
                        @click="blokirUser(user)"
                        class="flex items-center gap-2 cursor-pointer text-red-600"
                        :disabled="loading && processingUser === user.id"
                      >
                        <X class="h-4 w-4" />
                        <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Blokir' }}</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem 
                        @click="hapusUser(user)"
                        class="flex items-center gap-2 cursor-pointer text-red-600"
                        :disabled="loading && processingUser === user.id"
                      >
                        <Trash2 class="h-4 w-4" />
                        <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Hapus' }}</span>
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination -->
        <div v-if="props.users.data && props.users.data.length > 0" class="py-4 px-6 flex items-center justify-between border-t">
          <div class="text-sm text-muted-foreground">
            <span v-if="props.users.meta && props.users.meta.total">
              Menampilkan {{ props.users.data.length }} dari {{ props.users.meta.total }} pengguna
            </span>
            <span v-else>
              Menampilkan {{ props.users.data.length }} pengguna
            </span>
          </div>
          <div v-if="props.users.links && props.users.links.length > 2" class="flex gap-2">
            <Link 
              v-for="(link, i) in props.users.links.slice(1, -1)" 
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
  </AppLayout>
</template> 