<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    title: String,
    subtitle: String,
    quote: String,
    author: String,
    image: String,
})
</script>

<template>
    <div class="bg-background-light font-display flex min-h-screen flex-col text-slate-900">
        <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
            <div class="layout-container flex h-full grow flex-col">
                <!-- Header -->
                <header
                    class="border-sage-200 sticky top-0 z-50 flex items-center justify-between border-b border-solid bg-white/50 px-6 py-4 whitespace-nowrap backdrop-blur-md md:px-20"
                >
                    <Link :href="route('home')" class="text-sage-900 flex items-center gap-3">
                        <img class="w-20" src="/images/public/logo.png" alt="" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <span class="text-sage-600 hidden text-sm font-medium md:block"
                            >Need an account?</span
                        >
                        <Link
                            :href="route('public.register')"
                            class="text-sage-900 border-primary hover:text-primary border-b-2 text-sm font-bold transition-colors"
                            >Join Now</Link
                        >
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex flex-1 items-center justify-center px-4 py-12 md:py-20">
                    <div
                        class="grid w-full max-w-[1000px] grid-cols-1 items-center gap-12 lg:grid-cols-2"
                    >
                        <!-- Left Side: Form Area -->
                        <div class="order-2 flex flex-col gap-8 lg:order-1">
                            <div class="flex flex-col gap-4">
                                <span
                                    class="text-primary inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase"
                                >
                                    <span class="bg-primary h-px w-8"></span>
                                    Welcome Back
                                </span>
                                <h1
                                    class="text-sage-900 font-serif text-5xl leading-tight font-normal md:text-6xl"
                                >
                                    {{ title }}
                                </h1>
                                <p class="text-sage-600 max-w-md text-lg font-light">
                                    {{ subtitle }}
                                </p>
                            </div>

                            <!-- Form Slot -->
                            <div class="w-full max-w-md">
                                <slot />
                            </div>
                        </div>

                        <!-- Right Side: Dynamic Image & Quote -->
                        <div
                            class="relative order-1 h-[300px] w-full overflow-hidden rounded-3xl shadow-2xl lg:order-2 lg:h-[700px]"
                        >
                            <div
                                class="from-background-dark/60 absolute inset-0 z-10 bg-gradient-to-t via-transparent to-transparent"
                            ></div>
                            <img :src="image" class="absolute inset-0 h-full w-full object-cover" />
                            <div class="absolute bottom-10 left-10 z-20 max-w-xs text-white">
                                <span class="material-symbols-outlined text-primary mb-4 text-4xl"
                                    >format_quote</span
                                >
                                <p class="mb-4 font-serif text-2xl leading-relaxed italic">
                                    "{{ quote }}"
                                </p>
                                <p class="text-xs font-bold tracking-widest uppercase opacity-80">
                                    — {{ author }}
                                </p>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- Footer -->
                <footer
                    class="border-sage-200 border-t px-6 py-8 text-center md:px-20 lg:text-left"
                >
                    <div class="flex flex-col items-center justify-between gap-4 lg:flex-row">
                        <p class="text-sage-500 text-sm font-medium">
                            © 2024 Zen Sanctuary. All rights reserved.
                        </p>
                        <div class="text-sage-500 flex gap-8 text-sm font-medium">
                            <a class="hover:text-primary transition-colors" href="#">Privacy</a>
                            <a class="hover:text-primary transition-colors" href="#">Support</a>
                            <a class="hover:text-primary transition-colors" href="#"
                                >Sustainability</a
                            >
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-serif {
    font-family: 'Playfair Display', serif;
}
.font-display {
    font-family: 'Manrope', sans-serif;
}
</style>
