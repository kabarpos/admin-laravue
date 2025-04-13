<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();

interface WebsiteSettings {
  copyright?: string;
  [key: string]: any;
}

const page = usePage();
const websiteSettings = computed<WebsiteSettings>(() => page.props.websiteSettings as WebsiteSettings || {});
const copyright = computed(() => websiteSettings.value.copyright || `© ${new Date().getFullYear()} Admin Panel`);
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100" as-child>
                        <a :href="item.href" target="_blank" rel="noopener noreferrer">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <div class="px-3 py-2 text-xs text-muted-foreground">
                {{ copyright }}
            </div>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
