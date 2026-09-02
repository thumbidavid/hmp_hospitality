<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue"
import { Head, Link, usePage } from "@inertiajs/vue3"
import { ArrowRight, ArrowUpRight, Heart, MapPin, Users, Calendar, BedDouble, Download, ChevronLeft, ChevronRight, Check, X } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import PropertyCard from "@/Components/Public/PortfolioCard.vue"
import HmpAgencyCta from "@/Components/Public/HmpAgencyCta.vue"
import { useShortlistStore } from "@/Stores/shortlistStore"

const page = usePage()

const p = computed(() => page.props.property)
const related = computed(() => page.props.related || [])

const shortlistStore = useShortlistStore()

// State to track active primary image view index
const active = ref(0)

// Lightbox slideshow reactive states
const showLightbox = ref(false)
const lightboxIndex = ref(0)

const isShortlisted = computed(() => shortlistStore.has(p.value.id))

const handleToggleShortlist = () => {
    shortlistStore.toggle(p.value)
}

const facts = computed(() => [
    { icon: BedDouble, label: "Rooms", value: p.value.rooms },
    { icon: Users, label: "Largest event space", value: `${p.value.capacity} pax` },
    { icon: Calendar, label: "Best for", value: p.value.collection },
    { icon: MapPin, label: "Location", value: `${p.value.city}, ${p.value.country}` },
])

// Compile a unique, deduplicated list of all available images
const galleryImages = computed(() => {
    const list = []
    if (p.value.image) {
        list.push(p.value.image)
    }
    if (p.value.gallery && p.value.gallery.length > 0) {
        p.value.gallery.forEach(img => {
            if (img !== p.value.image && !list.includes(img)) {
                list.push(img)
            }
        })
    }
    return list
})

// Slideshow Navigation Methods
const openLightbox = (index) => {
    lightboxIndex.value = index
    showLightbox.value = true
}

const nextImage = () => {
    lightboxIndex.value = (lightboxIndex.value + 1) % galleryImages.value.length
}

const prevImage = () => {
    lightboxIndex.value = (lightboxIndex.value - 1 + galleryImages.value.length) % galleryImages.value.length
}

// Bind keyboard keys (Left, Right, Escape) for premium desktop navigation
const handleKeydown = (e) => {
    if (!showLightbox.value) return
    if (e.key === "ArrowRight") nextImage()
    if (e.key === "ArrowLeft") prevImage()
    if (e.key === "Escape") showLightbox.value = false
}

onMounted(() => {
    window.addEventListener("keydown", handleKeydown)
})

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown)
})

defineOptions({
    layout: Layout
})
</script>

<template>
    <div v-if="p">

        <Head>
            <title>{{ p.name }} — {{ p.city }}, {{ p.country }}</title>
            <meta name="description" :content="`Explore ${p.name} in ${p.city}, ${p.country}. ${p.blurb}`" />
            <meta name="keywords"
                :content="`${p.name}, ${p.city} hotel, ${p.country} resort, MICE venue, luxury lodging`" />
            <meta property="og:title" :content="`${p.name} — HMP Hospitality`" />
            <meta property="og:description" :content="p.blurb" />
            <meta property="og:image" :content="p.image" />
            <meta property="og:type" content="article" />
            <meta name="twitter:card" content="summary_large_image" />
        </Head>

        <!-- Gallery Hero -->
        <section class="pt-28">
            <div class="container-wide">
                <Link href="/portfolio" class="editorial-link text-sm">
                    <ChevronLeft class="h-4 w-4" /> Back to portfolio
                </Link>
            </div>

            <div class="container-wide mt-6">
                <!-- Outer 2-Column Grid -->
                <div class="grid gap-4 lg:grid-cols-[2.5fr_1fr] lg:h-[28rem]">

                    <!-- Main Active Image View (Left Column - Clickable) -->
                    <Reveal
                        class="relative h-64 lg:h-full overflow-hidden rounded-3xl shadow-sm bg-muted cursor-zoom-in">
                        <img :src="galleryImages[active] || p.image" :alt="p.name" @click="openLightbox(active)"
                            class="h-full w-full object-cover transition-all duration-500 hover:scale-[1.01]" />
                    </Reveal>

                    <!-- Sidebar Thumbnail list (Right Column - Constrained to 2 slots) -->
                    <div class="grid gap-4 grid-cols-2 lg:grid-cols-1">

                        <!-- Thumbnail 1 (Always maps to Index 1) -->
                        <button v-if="galleryImages[1]" @click="active = 1" :class="[
                            'relative overflow-hidden rounded-2xl h-24 lg:h-full cursor-pointer transition-all shadow-sm border border-border bg-muted',
                            active === 1 ? 'ring-2 ring-primary' : ''
                        ]">
                            <img :src="galleryImages[1]" alt="" class="h-full w-full object-cover" />
                        </button>

                        <!-- Thumbnail 2 (Launches slideshow if there are hidden images, otherwise acts as standard button) -->
                        <button v-if="galleryImages[2]" @click="galleryImages.length > 3 ? openLightbox(2) : active = 2"
                            :class="[
                                'relative overflow-hidden rounded-2xl h-24 lg:h-full cursor-pointer transition-all shadow-sm border border-border bg-muted',
                                active === 2 ? 'ring-2 ring-primary' : ''
                            ]">
                            <img :src="galleryImages[2]" alt="" class="h-full w-full object-cover" />

                            <!-- Tint Overlay & Count indicator (Visible if more than 3 images exist total) -->
                            <div v-if="galleryImages.length > 3"
                                class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white select-none transition-opacity hover:bg-black/50">
                                <span class="font-heading text-xl lg:text-3xl font-bold">
                                    +{{ galleryImages.length - 3 }}
                                </span>
                                <span
                                    class="text-[9px] uppercase tracking-wider text-white/80 hidden lg:block mt-1 font-medium">
                                    More Photos
                                </span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Header & Action Bar -->
        <section class="container-wide mt-10">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <Reveal>
                    <span class="eyebrow">{{ p.type }} · {{ p.collection }}</span>
                    <h1 class="mt-3 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">{{ p.name
                    }}</h1>
                    <p class="mt-3 flex items-center gap-1.5 text-muted-foreground">
                        <MapPin class="h-4 w-4 text-primary" /> {{ p.city }}, {{ p.country }} — {{ p.region }}
                    </p>
                    <p class="mt-5 max-w-2xl font-heading text-xl text-foreground/85 leading-snug">
                        {{ p.blurb }}
                    </p>
                </Reveal>

                <Reveal delay="0.1" class="flex flex-wrap items-center gap-3">
                    <button @click="handleToggleShortlist" :class="[
                        'inline-flex items-center gap-2 rounded-full border px-5 py-3 text-sm transition-colors cursor-pointer',
                        isShortlisted ? 'bg-primary text-primary-foreground border-primary' : 'border-foreground/15 hover:border-primary'
                    ]">
                        <Heart :class="['h-4 w-4', isShortlisted ? 'fill-current' : '']" />
                        {{ isShortlisted ? "In shortlist" : "Add to shortlist" }}
                    </button>
                    <Link :href="`/rfp?prop=${p.id}`" class="btn-primary">
                        Submit an RFP
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <a :href="p.website" target="_blank" rel="noreferrer" class="btn-ghost">
                        Visit Property Website
                        <ArrowUpRight class="h-4 w-4" />
                    </a>
                </Reveal>
            </div>
        </section>

        <!-- Facts Matrix -->
        <section class="container-wide mt-10">
            <div
                class="grid grid-cols-2 lg:grid-cols-4 gap-px rounded-3xl border border-border bg-border overflow-hidden">
                <div v-for="f in facts" :key="f.label" class="bg-card p-6">
                    <component :is="f.icon" class="h-5 w-5 text-primary" />
                    <p class="mt-4 text-xs uppercase tracking-wider text-muted-foreground">{{ f.label }}</p>
                    <p class="mt-1 font-heading text-xl">{{ f.value }}</p>
                </div>
            </div>
        </section>

        <!-- Descriptions and details -->
        <section class="container-wide grid gap-12 lg:grid-cols-[1.6fr_1fr] py-16 lg:py-24">
            <!-- Left Info Block Column -->
            <div class="space-y-12">
                <Reveal v-if="p.overview">
                    <h3 class="font-heading text-2xl mb-3">Overview</h3>
                    <p class="text-foreground/75 leading-relaxed">{{ p.overview }}</p>
                </Reveal>
                <Reveal v-if="p.accommodation">
                    <h3 class="font-heading text-2xl mb-3">Accommodation</h3>
                    <p class="text-foreground/75 leading-relaxed">{{ p.accommodation }}</p>
                </Reveal>
                <Reveal v-if="p.events">
                    <h3 class="font-heading text-2xl mb-3">Meetings & event facilities</h3>
                    <p class="text-foreground/75 leading-relaxed">{{ p.events }}</p>
                </Reveal>
                <Reveal v-if="p.dining">
                    <h3 class="font-heading text-2xl mb-3">Dining & leisure</h3>
                    <p class="text-foreground/75 leading-relaxed">{{ p.dining }}</p>
                </Reveal>
                <Reveal v-if="p.experiences && p.experiences.length > 0">
                    <h3 class="font-heading text-2xl mb-4">Key experiences</h3>
                    <ul class="grid gap-3 sm:grid-cols-2">
                        <li v-for="e in p.experiences" :key="e"
                            class="flex items-start gap-2.5 text-sm text-foreground/80">
                            <Check class="h-4 w-4 mt-0.5 text-primary shrink-0" /> {{ e }}
                        </li>
                    </ul>
                </Reveal>
                <Reveal v-if="p.sustainability">
                    <h3 class="font-heading text-2xl mb-3">Sustainability</h3>
                    <p class="text-foreground/75 leading-relaxed">{{ p.sustainability }}</p>
                </Reveal>
            </div>

            <!-- Right Sidebar Column -->
            <aside class="space-y-6 lg:sticky lg:top-28 self-start">
                <div class="rounded-3xl border border-border bg-card p-6">
                    <h3 class="font-heading text-xl">Representation by HMP</h3>
                    <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                        This property is represented by HMP Hospitality. Submit one RFP and our team will coordinate a
                        tailored proposal.
                    </p>
                    <div class="mt-5 flex flex-col gap-3">
                        <Link :href="`/rfp`" class="btn-primary justify-center">
                            Proceed to RFP Submission
                        </Link>
                        <button @click="handleToggleShortlist" class="btn-ghost justify-center cursor-pointer">
                            {{ isShortlisted ? "Remove from shortlist" : "Add to Shortlist" }}
                        </button>
                        <a :href="p.website" target="_blank" rel="noreferrer" class="editorial-link justify-center">
                            Visit property website
                            <ArrowUpRight class="h-4 w-4 arr" />
                        </a>
                    </div>
                    <div v-if="p.documents && p.documents.length > 0"
                        class="mt-6 grid gap-2 text-sm border-t border-border pt-4">
                        <a v-for="doc in p.documents" :key="doc.id" :href="doc.url" target="_blank" rel="noreferrer"
                            class="flex items-center gap-2 text-muted-foreground hover:text-primary transition-colors font-medium">
                            <Download class="h-4 w-4 text-primary" />Download {{ doc.label }} (PDF)
                        </a>
                    </div>
                </div>

                <!-- Interactive Map / Locator Block -->
                <div class="rounded-3xl overflow-hidden border border-border shadow-sm bg-background">
                    <!-- Render active Google Maps iframe if coordinates exist in the DB -->
                    <div v-if="p.latitude && p.longitude" class="aspect-[3/2] w-full h-full">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0"
                            marginwidth="0"
                            :src="`https://maps.google.com/maps?q=${p.latitude},${p.longitude}&z=15&output=embed`"
                            class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
                    </div>

                    <!-- Fallback static placeholder if coordinates are empty -->
                    <div v-else class="aspect-[3/2] bg-sand grid place-items-center text-center p-8">
                        <div>
                            <MapPin class="h-6 w-6 text-primary mx-auto" />
                            <p class="mt-2 font-heading text-lg">{{ p.city }}</p>
                            <p class="text-sm text-muted-foreground">Interactive map & nearby transport</p>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        <!-- Dynamic HMP Agency cross cta banner -->
        <HmpAgencyCta />

        <!-- Related Items Section -->
        <section class="bg-sand/60" v-if="related.length > 0">
            <div class="container-wide py-20">
                <Reveal class="mb-10 max-w-2xl">
                    <span class="eyebrow">You may also like</span>
                    <h2 class="mt-3 font-heading text-3xl sm:text-4xl">Related properties in the collection</h2>
                </Reveal>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Reveal v-for="(r, i) in related" :key="r.id" :delay="i * 0.06" class="h-full">
                        <!-- Remaps target payload with standard cards -->
                        <PropertyCard :it="r" />
                    </Reveal>
                </div>
            </div>
        </section>

        <!-- FULL-SCREEN NATIVE LIGHTBOX SLIDESHOW MODAL -->
        <div v-if="showLightbox"
            class="fixed inset-0 z-50 flex flex-col justify-between bg-black/95 p-4 md:p-8 animate-in fade-in duration-200">
            <!-- Header (Slide Counter & Close Button) -->
            <div class="flex items-center justify-between text-white z-10 w-full">
                <span class="text-sm font-medium bg-black/40 px-3 py-1.5 rounded-full select-none">
                    {{ lightboxIndex + 1 }} / {{ galleryImages.length }}
                </span>
                <button @click="showLightbox = false"
                    class="grid place-items-center h-10 w-10 rounded-full hover:bg-white/10 text-white cursor-pointer border-0 bg-transparent"
                    aria-label="Close Gallery">
                    <X class="h-6 w-6" />
                </button>
            </div>

            <!-- Slide View Area -->
            <div class="relative flex-1 flex items-center justify-center">
                <!-- Left arrow -->
                <button @click="prevImage"
                    class="absolute left-0 md:left-4 z-10 grid place-items-center h-12 w-12 rounded-full bg-black/40 hover:bg-white/10 text-white cursor-pointer border-0 shadow-sm"
                    aria-label="Previous Photo">
                    <ChevronLeft class="h-6 w-6" />
                </button>

                <!-- Active Enlarged Photo -->
                <img :src="galleryImages[lightboxIndex]"
                    class="max-h-[75vh] max-w-[90vw] object-contain rounded-lg shadow-2xl select-none animate-in zoom-in-95 duration-200"
                    alt="Gallery visual" />

                <!-- Right arrow -->
                <button @click="nextImage"
                    class="absolute right-0 md:right-4 z-10 grid place-items-center h-12 w-12 rounded-full bg-black/40 hover:bg-white/10 text-white cursor-pointer border-0 shadow-sm"
                    aria-label="Next Photo">
                    <ChevronRight class="h-6 w-6" />
                </button>
            </div>

            <!-- Footer descriptor -->
            <div class="text-center text-white/50 text-xs py-2 z-10 select-none">
                {{ p.name }} — Photo Gallery
            </div>
        </div>
    </div>
</template>