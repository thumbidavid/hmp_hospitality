<script setup>
import { ref, onMounted } from "vue"

const props = defineProps({
    delay: {
        type: Number,
        default: 0
    },
    y: {
        type: Number,
        default: 28
    }
})

const target = ref(null)
const isVisible = ref(false)

onMounted(() => {
    const observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                isVisible.value = true
                observer.unobserve(entry.target) // Trigger animation only once
            }
        },
        {
            rootMargin: "-60px" // Matches React Framer-Motion's margin
        }
    )
    if (target.value) {
        observer.observe(target.value)
    }
})
</script>

<template>
    <div ref="target" :class="['reveal-element', { 'revealed': isVisible }]" :style="{
        '--reveal-y': `${y}px`,
        '--reveal-delay': `${delay}s`
    }">
        <slot />
    </div>
</template>

<style scoped>
.reveal-element {
    opacity: 0;
    transform: translateY(var(--reveal-y, 28px));
    transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: var(--reveal-delay, 0s);
    will-change: transform, opacity;
}

.reveal-element.revealed {
    opacity: 1;
    transform: translateY(0);
}
</style>