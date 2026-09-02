<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"

const IMG = "https://images.unsplash.com/photo-1551882547-ff40ac63a5e1?auto=format&fit=crop&w=1600&q=80"
</script>

<template>
    <section class="container-wide py-20 lg:py-32">
        <div class="grid gap-10 lg:gap-16 lg:grid-cols-2 items-center">
            <!-- Left Panel (Visual Image Card) -->
            <Reveal>
                <div class="relative overflow-hidden rounded-2xl aspect-[4/3]">
                    <img :src="IMG" alt="Hospitality representation and buyer engagement" loading="lazy"
                        class="h-full w-full object-cover" />
                </div>
            </Reveal>

            <!-- Right Panel (Marketing Copy & Links) -->
            <Reveal :delay="0.08" class="max-w-xl">
                <span class="eyebrow">For Independent Hospitality</span>

                <h2 class="mt-5 font-heading text-3xl sm:text-4xl lg:text-5xl leading-tight text-balance">
                    Representation That Extends Your Reach
                </h2>

                <p class="mt-6 text-muted-foreground leading-relaxed">
                    We act as an extension of your commercial team — building relationships with buyers, developing
                    accounts and positioning your property or destination in priority markets.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <Link href="/services" class="btn-ghost">
                        Explore Our Services
                        <ArrowRight class="h-4 w-4" />
                    </Link>

                    <Link href="/partners" class="editorial-link">
                        Join the Portfolio
                        <ArrowRight class="h-4 w-4 arr" />
                    </Link>
                </div>
            </Reveal>
        </div>
    </section>
</template>