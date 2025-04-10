<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Plus, Trash2, Edit } from 'lucide-vue-next';

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
  <Head title="Manajemen Izin" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Izin</h1>
        <Button class="flex items-center gap-1.5 w-full sm:w-auto">
          <Plus class="h-4 w-4" />
          Tambah Izin
        </Button>
      </div>

      <div class="bg-card text-card-foreground rounded-xl shadow border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <div class="p-6">
          <h2 class="text-lg font-medium">Daftar Izin</h2>
          <p class="text-muted-foreground mt-1">Kelola izin yang akan diberikan kepada peran.</p>
        </div>
        
        <div class="border-t overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nama</TableHead>
                <TableHead>Digunakan Oleh</TableHead>
                <TableHead class="hidden md:table-cell">Tanggal Dibuat</TableHead>
                <TableHead class="w-[80px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="permission in permissions" :key="permission.id">
                <TableCell class="font-medium">{{ permission.name }}</TableCell>
                <TableCell>
                  <Badge>
                    {{ permission.roles_count }} peran
                  </Badge>
                </TableCell>
                <TableCell class="hidden md:table-cell">{{ formatDate(permission.created_at) }}</TableCell>
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
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer text-red-600" :disabled="permission.roles_count > 0">
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