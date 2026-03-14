<template>
    <div class="aside-logo-container d-flex align-items-center">
        <img :src="`${getAssetUrl()}icons/pwa-192x192.png`" alt="FinanceApp" class="h-40px me-3 logo-img" />
        <span class="fs-3 fw-bolder text-white logo-text">FinanceApp</span>
    </div>
</template>

<script setup lang="ts">
import { getAssetUrl } from '@/plugins/global';
</script>

<style scoped>
.aside-logo-container {
    padding: 0 1rem;
    transition: all 0.3s ease;
}

.logo-img {
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.logo-text {
    letter-spacing: 0.5px;
    white-space: nowrap;
}

/* Hide text when sidebar is minimized */
:deep(.aside-minimize) .logo-text {
    display: none;
}
</style>
