<script setup>
import { computed } from "vue"
import { Head, Link, usePage } from "@inertiajs/vue3"
import { ArrowRight, ArrowLeft } from "lucide-vue-next"

const HERO = "https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1800&q=80"

const page = usePage()

// Extract the path name from the active URL (e.g., "/some-page" -> "some-page")
const pageName = computed(() => {
    const path = page.url.split('?')[0] // Strip query parameters
    return path.startsWith('/') ? path.substring(1) : path
})

// Check if the authenticated user is an administrator via shared Inertia props
const isAdmin = computed(() => {
    return page.props.auth?.user?.role === 'admin'
})
</script>

<template>

    <Head>
        <title>Page Not Found</title>
        <meta name="description" content="The page you are looking for does not exist or has been moved." />
        <meta name="robots" content="noindex, nofollow" /> <!-- Protects from Google 404 indexing flags -->
    </Head>

    <section class="relative min-h-screen flex items-center overflow-hidden">
        <!-- Background Image -->
        <img :src="HERO" alt="" class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-ink/90 via-ink/80 to-ink/55" />

        <!-- Content Area -->
        <div class="container-wide relative z-10 py-24 text-white">
            <div class="max-w-xl">
                <span class="text-xs font-medium uppercase tracking-[0.3em] text-white/65">Error 404</span>

                <h1 class="mt-6 font-heading text-7xl sm:text-8xl lg:text-9xl leading-[0.95] font-medium">
                    Page Not<br /><span class="italic text-white/85">Found.</span>
                </h1>

                <p class="mt-8 text-lg text-white/80 leading-relaxed">
                    The page <span class="font-medium text-white">/{{ pageName || '' }}</span> could not be found in
                    this application, or has moved.
                </p>

                <!-- Action Buttons -->
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <Link href="/" class="btn-primary">
                        Return Home
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link href="/portfolio" class="btn-light">
                        Explore the Portfolio
                        <ArrowLeft class="h-4 w-4 rotate-180" />
                    </Link>
                </div>

                <!-- Admin Tooltip Card (Rendered only if the authenticated user is an admin) -->
                <div v-if="isAdmin"
                    class="mt-12 max-w-md rounded-xl border border-white/15 bg-white/5 backdrop-blur p-5">
                    <div class="flex items-start gap-3">
                        <span
                            class="grid place-items-center h-8 w-8 shrink-0 rounded-full bg-white/10 text-white text-xs font-semibold">!</span>
                        <div class="text-left space-y-1">
                            <p class="text-sm font-medium text-white">Admin Note</p>
                            <p class="text-sm text-white/70 leading-relaxed">
                                This could mean that the AI hasn't implemented this page yet. Ask it to implement it in
                                the chat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>