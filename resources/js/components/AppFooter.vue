<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface WebsiteSettings {
  copyright?: string;
  [key: string]: any;
}

const page = usePage();
const websiteSettings = computed<WebsiteSettings>(() => page.props.websiteSettings as WebsiteSettings || {});
const copyright = computed(() => websiteSettings.value.copyright || `© ${new Date().getFullYear()} Admin Panel`);
</script>

<template>
  <footer class="mt-auto border-t border-border py-4 px-6">
    <div class="container flex flex-col sm:flex-row justify-between items-center gap-2">
      <!-- Informasi copyright -->
      <div class="text-sm text-muted-foreground">
        {{ copyright }}
      </div>
      
      <!-- Links (opsional) -->
      <div class="flex gap-4">
        <a href="#" class="text-sm text-muted-foreground hover:text-foreground">Privacy Policy</a>
        <a href="#" class="text-sm text-muted-foreground hover:text-foreground">Terms of Service</a>
      </div>
    </div>
  </footer>
</template>
