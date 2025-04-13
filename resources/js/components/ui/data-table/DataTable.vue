<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import TablePagination from './TablePagination.vue';
import { cn } from '@/lib/utils';
import type { PaginatedData } from '@/types/models';

interface Column<T> {
  key: string;
  label: string;
  render?: (item: T, index: number) => any;
  cellClass?: string;
  headerClass?: string;
  hidden?: boolean | ((width: number) => boolean);
}

interface Props<T> {
  data?: T[] | PaginatedData<T>;
  columns: Column<T>[];
  keyField?: string;
  loaded?: boolean;
  tableClass?: string;
  rowClass?: string | ((item: T, index: number) => string);
  emptyMessage?: string;
  showPagination?: boolean;
}

const props = defineProps<Props<any>>();

const emit = defineEmits<{
  'page-click': [page: number]
}>();

// Handle viewport width for responsive columns
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1200);

onMounted(() => {
  const handleResize = () => {
    windowWidth.value = window.innerWidth;
  };
  
  window.addEventListener('resize', handleResize);
  
  onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
  });
});

// Compute which columns should be visible based on screen size
const visibleColumns = computed(() => {
  return props.columns.filter(column => {
    if (typeof column.hidden === 'function') {
      return !column.hidden(windowWidth.value);
    }
    return !column.hidden;
  });
});

// Determine if we're dealing with paginated data
const isPaginated = computed(() => {
  return props.data && typeof props.data === 'object' && 'meta' in props.data && 'data' in props.data;
});

// Extract items from data prop based on whether it's paginated
const items = computed(() => {
  if (!props.data) return [];
  return isPaginated.value ? (props.data as PaginatedData<any>).data : (props.data as any[]);
});

// Get pagination meta if available
const paginationMeta = computed(() => {
  if (isPaginated.value) {
    return (props.data as PaginatedData<any>).meta;
  }
  return null;
});

// Handle row class based on function or string
const getRowClass = (item: any, index: number) => {
  if (typeof props.rowClass === 'function') {
    return cn('border-b border-border/60 hover:bg-muted/20', props.rowClass(item, index));
  }
  return cn('border-b border-border/60 hover:bg-muted/20', props.rowClass || '');
};

const handlePageClick = (page: number) => {
  emit('page-click', page);
};
</script>

<template>
  <div class="w-full">
    <div class="overflow-x-auto">
      <Table :class="tableClass || 'w-full'">
        <TableHeader>
          <TableRow class="hover:bg-transparent border-b border-border">
            <TableHead 
              v-for="column in visibleColumns" 
              :key="column.key" 
              :class="column.headerClass || 'py-3 px-6 font-medium text-muted-foreground'"
            >
              {{ column.label }}
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="items.length > 0">
            <TableRow 
              v-for="(item, index) in items" 
              :key="item[keyField || 'id']"
              :class="getRowClass(item, index)"
            >
              <TableCell 
                v-for="column in visibleColumns" 
                :key="`${item[keyField || 'id']}-${column.key}`"
                :class="column.cellClass || 'py-3.5 px-6 align-middle'"
              >
                <template v-if="column.render">
                  <component :is="() => column.render!(item, index)" />
                </template>
                <template v-else>
                  {{ column.key.split('.').reduce((acc, k) => (acc && typeof acc === 'object') ? acc[k] : undefined, item) }}
                </template>
              </TableCell>
            </TableRow>
          </template>
          <TableRow v-else>
            <TableCell 
              :colspan="visibleColumns.length" 
              class="py-8 px-6 text-center text-muted-foreground"
            >
              {{ emptyMessage || 'Tidak ada data yang tersedia' }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    
    <TablePagination 
      v-if="showPagination && isPaginated && paginationMeta"
      :meta="paginationMeta"
      @page-click="handlePageClick"
    />
  </div>
</template> 