<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"

defineProps({
    image: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    headline: {
        type: String,
        required: true
    },
    copy: {
        type: String,
        required: true
    },
    buttonLabel: {
        type: String,
        required: true
    },
    to: {
        type: String,
        required: true
    },
    reverse: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <section class="container-wide py-12 lg:py-20">
        <!-- Scroll Reveal wrapper -->
        <Reveal class="grid gap-0 overflow-hidden rounded-2xl lg:grid-cols-2">
            <!-- Image Panel (Handles dynamic ordering when reversed) -->
            <div :class="[
                'relative min-h-[20rem] lg:min-h-[34rem]',
                reverse ? 'lg:order-2' : ''
            ]">
                <img :src="image" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover" />
            </div>

            <!-- Content Panel (Handles dynamic ordering when reversed) -->
            <div :class="[
                'bg-ink text-white p-10 lg:p-16 flex flex-col justify-center',
                reverse ? 'lg:order-1' : ''
            ]">
                <span class="text-[11px] font-medium uppercase tracking-[0.28em] text-white/60">
                    {{ label }}
                </span>

                <h2 class="mt-5 font-heading text-3xl sm:text-4xl lg:text-5xl leading-tight text-balance">
                    {{ headline }}
                </h2>

                <p class="mt-5 text-white/75 leading-relaxed max-w-md">
                    {{ copy }}
                </p>

                <div class="mt-8">
                    <Link :href="to" class="btn-primary">
                        {{ buttonLabel }}
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </Reveal>
    </section>
</template>