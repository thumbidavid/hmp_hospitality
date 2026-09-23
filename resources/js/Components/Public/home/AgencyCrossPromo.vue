<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"

const IMG = "/assets/public/images/HMP_Agency_Conference.jpeg"
</script>

<template>
    <section class="container-wide py-16 lg:py-24">
        <!-- Scroll Reveal Wrapper -->
        <Reveal class="relative overflow-hidden rounded-2xl">
            <!-- Background Image -->
            <img :src="IMG" alt="HMP Agency" loading="lazy" class="absolute inset-0 h-full w-full object-cover" />

            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-ink/90 via-ink/75 to-ink/45" />

            <!-- Content card -->
            <div class="relative p-8 sm:p-12 lg:p-16 max-w-xl text-white">
                <span class="text-[11px] font-medium uppercase tracking-[0.28em] text-white/65">
                    A Sister Business Unit
                </span>

                <h2 class="mt-4 font-heading text-3xl sm:text-4xl leading-tight text-balance">
                    Planning Beyond the Property?
                </h2>

                <p class="mt-5 text-white/80 leading-relaxed">
                    HMP Agency provides business travel, destination management, meetings and events services for
                    programmes requiring complete coordination.
                </p>

                <Link href="/hmp-agency" class="btn-light mt-8">
                    Discover HMP Agency
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </Reveal>
    </section>
</template>
