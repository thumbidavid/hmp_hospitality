<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="bg-background-light font-display min-h-screen">
        <!-- Navigation -->
        <nav class="border-sage-100 border-b bg-white"></nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
