<script setup>
import { watch, computed } from "vue"
import { usePage } from "@inertiajs/vue3"
import Navbar from "./Navbar.vue"
import Footer from "./Footer.vue"
import Toaster from "./Toaster.vue"

const page = usePage()

// Extract only the base path (e.g., "/portfolio" instead of "/portfolio?cat=Serviced")
const basePath = computed(() => page.url.split('?')[0])

// Only reset scroll when moving between entirely different pages
watch(
    basePath,
    () => {
        window.scrollTo({ top: 0 })
    }
)
</script>

<template>
    <div class="min-h-screen bg-background text-foreground flex flex-col">
        <!-- Persistent Header -->
        <Navbar />

        <!-- Page Injection Point -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Persistent Footer -->
        <Footer />

        <!-- Persistent Toast notification stack -->
        <Toaster />
    </div>
</template>