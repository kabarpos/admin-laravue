<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Plus, Check, X, Trash2, Edit, Shield } from 'lucide-vue-next';

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

// Data pengguna sample untuk tampilan awal
const users = ref([
  { 
    id: 1, 
    name: 'Admin', 
    email: 'admin@admin.com',
    status: 'active',
    created_at: '2023-01-01',
    roles: ['admin']
  },
  { 
    id: 2, 
    name: 'John Doe', 
    email: 'john@example.com',
    status: 'inactive',
    created_at: '2023-01-02',
    roles: ['user']
  },
  { 
    id: 3, 
    name: 'Jane Smith', 
    email: 'jane@example.com',
    status: 'active',
    created_at: '2023-01-03',
    roles: ['editor']
  },
  { 
    id: 4, 
    name: 'Bob Johnson', 
    email: 'bob@example.com',
    status: 'blocked',
    created_at: '2023-01-04',
    roles: ['user']
  }
]);

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
</script>

<template>
  <Head title="Manajemen Pengguna" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Pengguna</h1>
        <Button class="flex items-center gap-1.5 w-full sm:w-auto">
          <Plus class="h-4 w-4" />
          Tambah Pengguna
        </Button>
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
              <TableRow v-for="user in users" :key="user.id">
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
                    <Badge v-for="role in user.roles" :key="role" variant="outline" class="capitalize">
                      {{ role }}
                    </Badge>
                  </div>
                </TableCell>
                <TableCell class="hidden md:table-cell">{{ formatDate(user.created_at) }}</TableCell>
                <TableCell>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                        <Edit class="h-4 w-4" />
                        <span>Edit</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                        <Shield class="h-4 w-4" />
                        <span>Ubah Peran</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem v-if="user.status !== 'active'" class="flex items-center gap-2 cursor-pointer">
                        <Check class="h-4 w-4" />
                        <span>Aktifkan</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem v-if="user.status !== 'blocked'" class="flex items-center gap-2 cursor-pointer text-red-600">
                        <X class="h-4 w-4" />
                        <span>Blokir</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer text-red-600">
                        <Trash2 class="h-4 w-4" />
                        <span>Hapus</span>
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
  </AppLayout>
</template> 