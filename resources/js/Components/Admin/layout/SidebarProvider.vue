<template>
  <slot></slot>
</template>

<script setup>
import { useSidebarProvider } from '@/composables/useSidebar'

// just call it
useSidebarProvider()
</script>
