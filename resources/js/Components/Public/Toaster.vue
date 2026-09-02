<script setup>
import { X } from "lucide-vue-next"
import { useToast } from "./useToast"

const { toasts, dismiss } = useToast()
</script>

<template>
    <!-- Vue TransitionGroup automatically handles clean animations on insertion and deletion -->
    <TransitionGroup name="toast" tag="div"
        class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px] gap-3">
        <div v-for="toast in toasts" :key="toast.id" :class="[
            'group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all',
            toast.variant === 'destructive'
                ? 'border-destructive bg-destructive text-destructive-foreground'
                : 'border bg-background text-foreground'
        ]">
            <!-- Content -->
            <div class="grid gap-1">
                <div v-if="toast.title" class="text-sm font-semibold leading-none">
                    {{ toast.title }}
                </div>
                <div v-if="toast.description" class="text-sm opacity-90 leading-relaxed">
                    {{ toast.description }}
                </div>
            </div>

            <!-- Close Action -->
            <button @click="dismiss(toast.id)"
                class="absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity hover:text-foreground focus:opacity-100 focus:outline-none focus:ring-2 group-hover:opacity-100 group-[.destructive]:text-red-300 group-[.destructive]:hover:text-red-50"
                aria-label="Close">
                <X class="h-4 w-4" />
            </button>
        </div>
    </TransitionGroup>
</template>

<style scoped>
/* Smooth translation animations for incoming/outgoing toasts */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-enter-from {
    opacity: 0;
    transform: translateY(1rem) scale(0.95);
}

.toast-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>