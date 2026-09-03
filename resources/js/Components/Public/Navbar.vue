<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue"
import { Link, usePage, router } from "@inertiajs/vue3"
import { Menu, X, Search, ArrowRight } from "lucide-vue-next"
import { useShortlistStore } from "@/Stores/shortlistStore"

const links = [
    { label: "Home", path: "/" },
    { label: "Portfolio", path: "/portfolio" },
    { label: "About Us", path: "/about" },
    { label: "Services", path: "/services" },
    { label: "Partners", path: "/partners" },
    { label: "Contact", path: "/contact" },
]

const page = usePage()
const shortlistStore = useShortlistStore()

const scrolled = ref(false)
const open = ref(false)
const overHero = ref(true)

// Get the count reactively from Pinia store
const count = computed(() => shortlistStore.items.length)

// Match React's strict path checks (stripping any query params)
const currentPath = computed(() => page.url.split('?')[0])
const isHome = computed(() => currentPath.value === "/")

// Intelligent parent path matching helper
const isActive = (path) => {
    if (path === "/") {
        return currentPath.value === "/"
    }
    if (path === "/portfolio") {
        return currentPath.value.startsWith("/portfolio") || currentPath.value.startsWith("/destinations")
    }
    return currentPath.value.startsWith(path)
}

// Scroll tracking state
const onScroll = () => {
    scrolled.value = window.scrollY > 24
    overHero.value = window.scrollY < 480 && isHome.value
}

onMounted(() => {
    onScroll()
    window.addEventListener("scroll", onScroll, { passive: true })
})

onUnmounted(() => {
    window.removeEventListener("scroll", onScroll)
})

// Close the mobile menu automatically on route change
watch(
    () => page.url,
    () => {
        open.value = false
    }
)

const transparent = computed(() => overHero.value && isHome.value)
const dark = computed(() => transparent.value)

// Navigation helper
const navigateTo = (path) => {
    router.visit(path)
}
</script>

<template>
    <header :class="[
        'fixed inset-x-0 top-0 z-50 transition-all duration-500',
        transparent ? 'bg-transparent' : 'bg-background/85 backdrop-blur-xl border-b border-border'
    ]">
        <div class="container-wide flex h-20 items-center justify-between">
            <!-- Dynamic Image Logo (Desktop/Mobile) -->
            <Link href="/" class="flex items-center group">
                <img :src="dark ? '/logo_white.png' : '/logo.png'" alt="HMP Hospitality"
                    class="h-10 w-auto object-contain transition-all duration-300" />
            </Link>

            <!-- Desktop Links -->
            <nav class="hidden lg:flex items-center gap-9 h-full">
                <Link v-for="l in links" :key="l.path" :href="l.path" :class="[
                    'text-sm font-medium transition-colors relative py-1.5 flex items-center h-full',
                    transparent
                        ? (isActive(l.path) ? 'text-white font-semibold' : 'text-white/85 hover:text-white')
                        : (isActive(l.path) ? 'text-primary font-semibold' : 'text-ink/70 hover:text-ink')
                ]">
                    {{ l.label }}
                    <!-- Subtle active page bottom bar indicator -->
                    <span v-if="isActive(l.path)" :class="[
                        'absolute bottom-4 left-0 right-0 h-0.5 rounded-full',
                        transparent ? 'bg-white' : 'bg-primary'
                    ]" />
                </Link>
            </nav>

            <!-- Desktop Actions -->
            <div class="hidden lg:flex items-center gap-3">


                <Link v-if="count > 0" href="/rfp" :class="[
                    'text-sm flex items-center gap-1.5',
                    transparent ? 'text-white' : 'text-ink'
                ]">
                    <span
                        class="grid place-items-center h-6 min-w-6 px-1.5 rounded-full bg-primary text-primary-foreground text-xs font-semibold">
                        {{ count }}
                    </span>
                    Shortlist
                </Link>

                <button @click="navigateTo('/rfp')" class="btn-primary">
                    Submit RFP
                    <ArrowRight class="h-4 w-4" />
                </button>
            </div>

            <!-- Mobile Menu Open Trigger -->
            <button @click="open = true" :class="[
                'lg:hidden grid place-items-center h-10 w-10 rounded-full border',
                transparent ? 'border-white/25 text-white' : 'border-ink/20 text-ink'
            ]" aria-label="Menu">
                <Menu class="h-5 w-5" />
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div v-if="open" class="lg:hidden fixed inset-0 z-50 bg-ink text-white">
            <div class="flex h-20 items-center justify-between container-wide">
                <!-- Mobile White Image Logo -->
                <Link href="/" class="flex items-center group">
                    <img src="/logo_white.png" alt="HMP Hospitality" class="h-10 w-auto object-contain" />
                </Link>

                <!-- Close Button -->
                <button @click="open = false"
                    class="grid place-items-center h-10 w-10 rounded-full border border-white/25 text-white"
                    aria-label="Close">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <div class="container-wide pb-10">
                <span class="block px-1 text-[11px] font-medium uppercase tracking-[0.28em] text-primary">Menu</span>
                <nav class="mt-3 flex flex-col border-t border-white/10">
                    <Link v-for="l in links" :key="l.path" :href="l.path" :class="[
                        'flex items-center justify-between font-heading text-3xl py-4 border-b border-white/10 transition-colors',
                        isActive(l.path) ? 'text-primary' : 'text-white hover:text-white/80'
                    ]">
                        {{ l.label }}
                        <span v-if="isActive(l.path)" class="h-2 w-2 rounded-full bg-primary" />
                    </Link>
                </nav>

                <Link href="/rfp" class="btn-primary mt-8 w-full justify-center">
                    Submit RFP
                    <ArrowRight class="h-4 w-4" />
                </Link>
                <Link href="/partners" class="btn-light mt-3 w-full justify-center">
                    Become a Partner
                </Link>

                <div class="mt-10 space-y-1 text-sm text-white/55">
                    <p>hello@hmphospitality.co · +254 703 720 000</p>
                    <p class="leading-relaxed">6th Floor, MJ1 Business Park, Westlands Road, Nairobi</p>
                </div>
            </div>
        </div>
    </header>
</template>