<template>
  <textarea
    :class="[
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none',
      error ? 'border-destructive focus-visible:ring-destructive' : '',
      modelValue ? 'border-primary' : ''
    ]"
    ref="textarea"
    :value="modelValue"
    @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    :disabled="disabled"
    :rows="rows"
    :placeholder="placeholder"
  />
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: ''
  },
  rows: {
    type: [Number, String],
    default: 4
  },
  error: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const textarea = ref<HTMLTextAreaElement | null>(null)

// Resize textarea on mount
onMounted(() => {
  if (textarea.value) {
    adjustHeight(textarea.value)
  }
})

// Watch model value changes to adjust height
watch(() => textarea.value?.value, () => {
  if (textarea.value) {
    adjustHeight(textarea.value)
  }
})

// Function to adjust textarea height based on content
const adjustHeight = (element: HTMLTextAreaElement) => {
  // Reset to default height first
  element.style.height = 'auto'
  // Set new height based on scrollHeight
  element.style.height = `${element.scrollHeight}px`
}
</script>

<script lang="ts">
export const Textarea = {}
</script> 