<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { User, KeyRound, Palette } from 'lucide-vue-next';

const tabNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: '/settings/profile',
        icon: User
    },
    {
        title: 'Password',
        href: '/settings/password',
        icon: KeyRound
    },
    {
        title: 'Appearance',
        href: '/settings/appearance',
        icon: Palette
    },
];

// Gunakan ref untuk menyimpan path
const currentPath = ref('');

// Tentukan tab aktif berdasarkan URL saat ini
const activeTab = computed(() => {
    const pathParts = currentPath.value.split('/');
    return pathParts[pathParts.length - 1] || 'profile';
});

onMounted(() => {
    // Ambil path dari window.location saat component di-mount
    currentPath.value = window.location.pathname;
});
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Pengaturan Profil" description="Kelola pengaturan profil dan akun Anda" />

        <div class="mt-6 space-y-8">
            <!-- Tab Navigation dengan Komponen Shadcn UI -->
            <Tabs :default-value="activeTab" class="w-full">
                <TabsList class="w-full md:w-auto mb-6">
                    <TabsTrigger 
                        v-for="item in tabNavItems" 
                        :key="item.href" 
                        :value="item.href.split('/').pop() || ''"
                        class="flex items-center gap-2"
                        as-child
                    >
                        <Link :href="item.href">
                            <div class="flex items-center gap-2">
                                <component :is="item.icon" class="h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </div>
                        </Link>
                    </TabsTrigger>
                </TabsList>
            </Tabs>

            <Separator class="my-6" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
