<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import SearchPanel from "./SearchPanel.vue"

const HERO = "https://media.base44.com/images/public/6a0f7d8de59727e2def89502/89a0e9bff_generated_image.png"
</script>

<template>
    <section class="relative min-h-[92vh] flex flex-col justify-end overflow-hidden">
        <!-- Background Image with Fade & Scale Animation -->
        <img :src="HERO" alt="A curated hospitality destination"
            class="absolute inset-0 h-full w-full object-cover animate-kenburns animate-hero-img" />

        <!-- Gradients -->
        <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/35 to-ink/20" />
        <div class="absolute inset-0 bg-gradient-to-r from-ink/55 to-transparent" />

        <!-- Content Area with slide-up fade animation -->
        <div class="container-wide relative z-10 pt-32 pb-44 text-white">
            <div class="max-w-3xl animate-hero-text">
                <span class="text-[11px] font-medium uppercase tracking-[0.32em] text-white/75">
                    HMP Hospitality · Africa
                </span>

                <h1
                    class="mt-6 font-heading text-[2.9rem] sm:text-6xl lg:text-8xl leading-[1.0] font-medium text-balance">
                    Exceptional Places.<br />
                    <span class="italic text-white/90">Meaningful Connections.</span>
                </h1>

                <p class="mt-8 max-w-xl text-base sm:text-lg leading-relaxed text-white/85">
                    Discover a curated collection of independent hotels, resorts, venues, serviced residences and
                    destinations represented across priority global markets.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <Link href="/portfolio" class="btn-primary">
                        Explore the Portfolio
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link href="/rfp" class="btn-light">
                        Submit an RFP
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Panel container -->
    <div class="container-wide relative z-20 -mt-28 pb-2">
        <SearchPanel />
    </div>
</template>

<style scoped>
/* Keyframe definition for hardware-accelerated image fade in */
@keyframes heroFadeIn {
    from {
        opacity: 0.4;
        transform: scale(1.05);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Keyframe definition for text block entry animation */
@keyframes heroSlideUp {
    from {
        opacity: 0;
        transform: translateY(28px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Apply animations to classes */
.animate-hero-img {
    animation: heroFadeIn 1.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    will-change: transform, opacity;
}

.animate-hero-text {
    animation: heroSlideUp 1s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    will-change: transform, opacity;
}
</style>