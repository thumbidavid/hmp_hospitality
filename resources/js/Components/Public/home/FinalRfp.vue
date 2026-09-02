<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"

const IMG = "https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=2000&q=80"
</script>

<template>
    <section class="relative overflow-hidden">
        <!-- Background Cover Image -->
        <img :src="IMG" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover" />

        <!-- Dark Overlay Gradient matching brand aesthetics -->
        <div class="absolute inset-0 bg-gradient-to-r from-ink/90 via-ink/75 to-ink/55" />

        <!-- Centered Marketing Content -->
        <Reveal class="relative container-wide py-28 lg:py-40 text-center">
            <span class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/70">
                Submit an RFP
            </span>

            <h2
                class="mx-auto mt-5 max-w-3xl font-heading text-3xl sm:text-5xl lg:text-6xl leading-[1.05] text-white text-balance">
                Tell Us What You're Planning
            </h2>

            <p class="mx-auto mt-6 max-w-xl text-white/80 leading-relaxed">
                Share your requirements and we will connect you with the properties and destinations best suited to your
                programme.
            </p>

            <!-- Routing Buttons -->
            <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                <Link href="/rfp" class="btn-primary">
                    Submit an RFP
                    <ArrowRight class="h-4 w-4" />
                </Link>
                <Link href="/contact" class="btn-light">
                    Contact Our Team
                </Link>
            </div>
        </Reveal>
    </section>
</template>