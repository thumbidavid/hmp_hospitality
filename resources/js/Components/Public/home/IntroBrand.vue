<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"
</script>

<template>
    <section class="container-wide pt-32 pb-24 lg:pt-44 lg:pb-32">
        <Reveal class="max-w-2xl">
            <span class="eyebrow">Welcome to HMP Hospitality</span>

            <h2 class="mt-5 font-heading text-4xl sm:text-5xl lg:text-6xl leading-[1.05] text-balance">
                Independent Hospitality. Global Markets.
            </h2>

            <p class="mt-7 text-lg text-muted-foreground leading-relaxed max-w-xl">
                HMP Hospitality provides bespoke sales and marketing representation for independent hotels, resorts,
                lodges, venues, serviced residences and destinations. We connect distinctive hospitality businesses with
                qualified travel professionals, corporate buyers and meeting planners.
            </p>

            <div class="mt-9 flex flex-wrap items-center gap-4">
                <Link href="/about" class="btn-ghost">
                    Discover Our Story
                    <ArrowRight class="h-4 w-4" />
                </Link>

                <Link href="/services" class="editorial-link">
                    Explore Our Services
                    <ArrowRight class="h-4 w-4 arr" />
                </Link>
            </div>
        </Reveal>
    </section>
</template>