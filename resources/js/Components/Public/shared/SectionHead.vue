<script setup>
import Reveal from "./Reveal.vue"

defineProps({
    eyebrow: String,
    title: String,
    sub: String,
    center: {
        type: Boolean,
        default: false
    },
    light: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <Reveal :class="['mb-12', center ? 'text-center mx-auto max-w-2xl' : 'max-w-2xl']">
        <span v-if="eyebrow" class="eyebrow">{{ eyebrow }}</span>

        <h2 :class="[
            'mt-3 font-heading text-3xl sm:text-4xl lg:text-[2.75rem] leading-[1.08] text-balance',
            light ? 'text-white' : 'text-foreground'
        ]">
            {{ title }}
        </h2>

        <p v-if="sub" :class="[
            'mt-5 text-base leading-relaxed',
            light ? 'text-ink-fg/70' : 'text-muted-foreground'
        ]">
            {{ sub }}
        </p>
    </Reveal>
</template>