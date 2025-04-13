<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { computed } from 'vue';
import AppFooter from '@/components/AppFooter.vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
    showFooter?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showFooter: true
});

const className = computed(() => props.class);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className" class="flex flex-col">
        <div class="flex flex-col flex-1 min-h-[calc(100vh-4rem)]">
            <slot />
        </div>
        <AppFooter v-if="showFooter" />
    </SidebarInset>
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <div class="flex flex-col flex-1">
            <slot />
        </div>
        <AppFooter v-if="showFooter" />
    </main>
</template>
