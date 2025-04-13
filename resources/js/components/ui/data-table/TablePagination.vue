<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginationMeta } from '@/types/models';

const props = defineProps<{
  meta: PaginationMeta;
}>();

const emit = defineEmits<{
  'page-click': [page: number];
}>();

const onPageClick = (page: number) => {
  emit('page-click', page);
};
</script>

<template>
  <div class="py-4 px-6 flex items-center justify-between border-t">
    <div class="text-sm text-muted-foreground">
      <span v-if="meta.total">
        Menampilkan {{ meta.from || 0 }} sampai {{ meta.to || 0 }} dari {{ meta.total }} item
      </span>
      <span v-else>
        Tidak ada data
      </span>
    </div>
    
    <div v-if="meta.last_page > 1" class="flex gap-1">
      <!-- First Page -->
      <button 
        v-if="meta.current_page > 1"
        @click="onPageClick(1)"
        class="px-3 py-1 rounded text-sm border border-input cursor-pointer hover:bg-accent"
        :disabled="meta.current_page === 1"
      >
        &laquo;
      </button>
      
      <!-- Previous Page -->
      <button 
        v-if="meta.current_page > 1"
        @click="onPageClick(meta.current_page - 1)"
        class="px-3 py-1 rounded text-sm border border-input cursor-pointer hover:bg-accent"
        :disabled="meta.current_page === 1"
      >
        &lsaquo;
      </button>
      
      <!-- Page Links -->
      <template v-for="page in meta.last_page" :key="page">
        <!-- Only show 5 pages around current page for better UI -->
        <button
          v-if="
            page === 1 || 
            page === meta.last_page || 
            (page >= meta.current_page - 2 && page <= meta.current_page + 2)
          "
          @click="onPageClick(page)"
          class="px-3 py-1 rounded text-sm border cursor-pointer"
          :class="page === meta.current_page ? 
            'bg-primary text-primary-foreground border-primary' : 
            'border-input hover:bg-accent'"
        >
          {{ page }}
        </button>
        
        <!-- Ellipsis -->
        <span 
          v-else-if="
            (page === 2 && meta.current_page > 4) || 
            (page === meta.last_page - 1 && meta.current_page < meta.last_page - 3)
          "
          class="px-3 py-1 text-sm text-muted-foreground"
        >
          ...
        </span>
      </template>
      
      <!-- Next Page -->
      <button 
        v-if="meta.current_page < meta.last_page"
        @click="onPageClick(meta.current_page + 1)"
        class="px-3 py-1 rounded text-sm border border-input cursor-pointer hover:bg-accent"
        :disabled="meta.current_page === meta.last_page"
      >
        &rsaquo;
      </button>
      
      <!-- Last Page -->
      <button 
        v-if="meta.current_page < meta.last_page"
        @click="onPageClick(meta.last_page)"
        class="px-3 py-1 rounded text-sm border border-input cursor-pointer hover:bg-accent"
        :disabled="meta.current_page === meta.last_page"
      >
        &raquo;
      </button>
    </div>
  </div>
</template> 