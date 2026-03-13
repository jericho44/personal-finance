<template>
    <router-view v-slot="{ Component, route }">
        <component :is="Component" :key="route.fullPath" />
    </router-view>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();

onMounted(() => {
    themeStore.applyTheme();
});
</script>
