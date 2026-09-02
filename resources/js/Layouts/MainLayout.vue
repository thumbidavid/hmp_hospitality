<template>
    <div class="min-h-screen flex flex-col">
        <div class="print:hidden">
            <PromoBannerStrip />
            <NewsTicker />
            <Navbar />
        </div>

        <main class="flex-1">
            <!-- The content will be injected here -->
            <slot />
        </main>

        <div class="print:hidden">
            <Footer />
        </div>
    </div>
</template>

<script setup>
import NewsTicker from "@/Components/Public/NewsTicker.vue";
import Navbar from "@/Components/Public/Navbar.vue";
import Footer from "@/Components/Public/Footer.vue";
import PromoBannerStrip from "@/Components/Public/PromoBannerStrip.vue";
</script>