<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Fungsi untuk mengambil URL lengkap dari path foto profil
const getAvatarUrl = (path: string | null | undefined): string | null => {
    if (!path) return null;
    
    // Jika sudah berupa URL lengkap, gunakan langsung
    if (path.startsWith('http')) return path;
    
    // Jika path relatif, tambahkan URL storage
    return `/storage/${path}`;
};

// Compute whether we should show the avatar image
const showAvatar = computed(() => {
    return (props.user.profile_photo_path && props.user.profile_photo_path !== '') || 
           (props.user.avatar && props.user.avatar !== '');
});

// Get the avatar URL from either profile_photo_path or legacy avatar field
const avatarUrl = computed(() => {
    if (props.user.profile_photo_path) {
        return getAvatarUrl(props.user.profile_photo_path);
    }
    return props.user.avatar || null;
});
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar && avatarUrl" :src="avatarUrl" :alt="user.name" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
