<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Plus, Trash2, Edit, Key, Eye } from 'lucide-vue-next';

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

// Data peran sample untuk tampilan awal
const roles = ref([
  { 
    id: 1, 
    name: 'admin',
    permissions: ['view users', 'create users', 'edit users', 'delete users', 'approve users', 'reject users', 'view roles', 'create roles', 'edit roles', 'view permissions', 'create permissions', 'edit permissions'],
    users_count: 1,
    created_at: '2023-01-01'
  },
  { 
    id: 2, 
    name: 'user',
    permissions: ['view own profile', 'edit own profile'],
    users_count: 2,
    created_at: '2023-01-02'
  },
  { 
    id: 3, 
    name: 'editor',
    permissions: ['view users', 'view content', 'create content', 'edit content', 'delete content'],
    users_count: 1,
    created_at: '2023-01-03'
  }
]);

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
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nama</TableHead>
                <TableHead>Izin</TableHead>
                <TableHead class="hidden md:table-cell">Jumlah Pengguna</TableHead>
                <TableHead class="hidden md:table-cell">Tanggal Dibuat</TableHead>
                <TableHead class="w-[80px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="role in roles" :key="role.id">
                <TableCell class="font-medium capitalize">{{ role.name }}</TableCell>
                <TableCell>
                  <div class="flex gap-1 flex-wrap">
                    <Badge variant="outline">
                      {{ role.permissions.length }} izin
                    </Badge>
                    <Link v-if="role.permissions.length > 0" :href="route('admin.roles.show', role.id)" class="md:hidden">
                      <Badge variant="outline">
                        <span class="text-xs text-muted-foreground hover:text-foreground cursor-pointer" title="Lihat semua izin">
                          Lihat detail
                        </span>
                      </Badge>
                    </Link>
                    <div class="hidden md:flex md:gap-1 md:flex-wrap">
                      <Badge v-if="role.permissions.length > 3" variant="outline" class="bg-muted">
                        +{{ role.permissions.length }} izin
                      </Badge>
                      <Badge v-else v-for="permission in role.permissions.slice(0, 3)" :key="permission" variant="outline" class="text-xs">
                        {{ permission }}
                      </Badge>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="hidden md:table-cell">{{ role.users_count }}</TableCell>
                <TableCell class="hidden md:table-cell">{{ formatDate(role.created_at) }}</TableCell>
                <TableCell>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon" class="cursor-pointer">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <Link :href="route('admin.roles.show', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                          <Eye class="h-4 w-4" />
                          <span>Lihat Detail</span>
                        </DropdownMenuItem>
                      </Link>
                      <Link :href="route('admin.roles.edit', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer">
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                      </Link>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer" :disabled="role.name === 'admin'">
                        <Key class="h-4 w-4" />
                        <span>Kelola Izin</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer text-red-600" :disabled="role.name === 'admin'">
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