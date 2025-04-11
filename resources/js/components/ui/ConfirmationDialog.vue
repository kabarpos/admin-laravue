<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { ref, watch } from 'vue';

const props = defineProps<{
  open: boolean;
  title: string;
  description?: string;
  confirmLabel?: string;
  cancelLabel?: string;
  dangerMode?: boolean;
  loading?: boolean;
  icon?: any; // Komponen ikon dari lucide-vue-next
}>();

const emit = defineEmits<{
  'update:open': [value: boolean];
  'confirm': [];
  'cancel': [];
}>();

const internalOpen = ref(props.open);

watch(() => props.open, (newValue) => {
  internalOpen.value = newValue;
});

watch(() => internalOpen.value, (newValue) => {
  emit('update:open', newValue);
});

const handleConfirm = () => {
  emit('confirm');
};

const handleCancel = () => {
  internalOpen.value = false;
  emit('cancel');
};
</script>

<template>
  <Dialog :open="internalOpen" @update:open="internalOpen = $event">
    <DialogContent class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 text-gray-900 dark:text-white max-w-[400px]">
      <DialogHeader>
        <DialogTitle class="text-xl flex items-center gap-2">
          <component :is="icon" v-if="icon" class="h-5 w-5" :class="{ 'text-red-500': dangerMode, 'text-green-500': !dangerMode }" />
          {{ title }}
        </DialogTitle>
        <DialogDescription class="text-gray-500 dark:text-gray-300 mt-2">
          <slot>
            {{ description }}
          </slot>
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="mt-6 flex gap-3">
        <Button 
          variant="outline" 
          @click="handleCancel" 
          :disabled="loading" 
          class="bg-transparent text-gray-800 dark:text-white border-gray-300 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-800"
        >
          {{ cancelLabel || 'Batal' }}
        </Button>
        <Button 
          @click="handleConfirm" 
          :disabled="loading"
          :class="{
            'bg-green-600 hover:bg-green-700 text-white flex items-center gap-2': !dangerMode,
            'bg-red-600 hover:bg-red-700 text-white flex items-center gap-2': dangerMode
          }"
        >
          <component :is="icon" v-if="icon && !loading" class="h-4 w-4" />
          <slot name="confirm-content">
            {{ loading ? 'Memproses...' : confirmLabel || 'Konfirmasi' }}
          </slot>
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template> 