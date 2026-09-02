<script setup>
import { useSidebar } from '@/composables/useSidebar';
const { isMobileOpen, toggleMobileSidebar } = useSidebar();
</script>

<template>
    <!-- Backdrop for mobile sidebar -->
    <div
        v-if="isMobileOpen"
        @click="toggleMobileSidebar"
        class="fixed inset-0 z-99998 bg-black/60 lg:hidden"
    ></div>
</template>
