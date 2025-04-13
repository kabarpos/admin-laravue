<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface WebsiteSettings {
  siteName?: string;
  logoUrl?: string;
  [key: string]: any;
}

const page = usePage();
const websiteSettings = computed<WebsiteSettings>(() => page.props.websiteSettings as WebsiteSettings || {});
const logoUrl = computed(() => websiteSettings.value.logoUrl);
const siteName = computed(() => websiteSettings.value.siteName || page.props.name || 'Admin Panel');
</script>

<template>
    <div v-if="logoUrl" class="flex aspect-square size-8 items-center justify-center rounded-md">
        <img :src="logoUrl" alt="Logo" class="max-h-full max-w-full object-contain" />
    </div>
    <div v-else class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">{{ siteName }}</span>
    </div>
</template>
