<script setup>
import { ref, computed } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import { ArrowRight, ChevronLeft, ChevronRight, MapPin } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"

const page = usePage()

// Track the scroller container
const scroller = ref(null)

// Left / Right Scroll trigger
const scroll = (dir) => {
    const el = scroller.value
    if (el) {
        el.scrollBy({ left: dir * (el.clientWidth * 0.8), behavior: "smooth" })
    }
}

// Compute dynamic destinations with mock values as fallback
const destinationsList = computed(() => {
    if (page.props.destinations && page.props.destinations.length > 0) {
        return page.props.destinations
    }

    // Fail-safe layout preview
    return [
        {
            id: 1,
            name: "Nairobi",
            country: "Kenya",
            region: "East Africa",
            image: "https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=800&q=80",
            intro: "A vibrant business hub and the only city in the world with a national park on its doorstep.",
            properties_count: 3
        },
        {
            id: 2,
            name: "Cape Town",
            country: "South Africa",
            region: "Southern Africa",
            image: "https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=800&q=80",
            intro: "A breathtaking coastal destination famous for Table Mountain, coastal views, and exceptional venues.",
            properties_count: 5
        },
        {
            id: 3,
            name: "Zanzibar",
            country: "Tanzania",
            region: "East Africa",
            image: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80",
            intro: "A tropical island paradise with a rich cultural heritage, white sandy beaches, and premium resorts.",
            properties_count: 2
        }
    ]
})
</script>

<template>
    <section class="bg-sand/40">
        <div class="container-wide py-20 lg:py-28">
            <!-- Section Header -->
            <div class="flex items-end justify-between gap-6">
                <Reveal class="max-w-xl">
                    <span class="eyebrow">Explore by Destination</span>
                    <h2 class="mt-4 font-heading text-3xl sm:text-4xl lg:text-5xl leading-tight text-balance">
                        Remarkable cities, coastlines and business-event destinations.
                    </h2>
                    <p class="mt-5 text-muted-foreground leading-relaxed">
                        Discover represented properties and experiences across destinations with exceptional
                        hospitality, accessibility and programme settings.
                    </p>
                </Reveal>

                <!-- Navigation scroll buttons (Desktop only) -->
                <div class="hidden lg:flex items-center gap-2 shrink-0">
                    <button @click="scroll(-1)"
                        class="grid place-items-center h-11 w-11 rounded-full border border-ink/15 text-ink hover:border-primary hover:text-primary transition-colors cursor-pointer"
                        aria-label="Scroll left">
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button @click="scroll(1)"
                        class="grid place-items-center h-11 w-11 rounded-full border border-ink/15 text-ink hover:border-primary hover:text-primary transition-colors cursor-pointer"
                        aria-label="Scroll right">
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Scroller Container -->
            <div ref="scroller"
                class="mt-10 flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 lg:pb-2 -mx-5 lg:mx-0 px-5 lg:px-0 scroll-smooth">
                <Link v-for="(d, i) in destinationsList" :key="d.id" :href="`/destinations/${d.id}`"
                    class="group relative shrink-0 w-[78%] sm:w-[44%] lg:w-[30%] snap-start">
                    <Reveal :delay="(i % 3) * 0.05" class="h-full">
                        <div class="relative overflow-hidden rounded-2xl aspect-[4/5]">
                            <!-- Image with Scale Hover Zoom -->
                            <img :src="d.image" :alt="d.name" loading="lazy"
                                class="h-full w-full object-cover img-rise" />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/20 to-transparent" />

                            <!-- Properties Count Badge -->
                            <div class="absolute left-5 top-5">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium text-ink shadow-sm">
                                    {{ d.properties_count }} represented
                                    {{ d.properties_count === 1 ? 'member' : 'members' }}
                                </span>
                            </div>

                            <!-- Bottom Label Card -->
                            <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                                <span class="flex items-center gap-1.5 text-xs text-white/75">
                                    <MapPin class="h-3.5 w-3.5 text-primary" /> {{ d.country }} · {{ d.region }}
                                </span>
                                <h3 class="mt-2 font-heading text-2xl lg:text-3xl">{{ d.name }}</h3>
                                <p class="mt-2 text-sm text-white/80 line-clamp-2 leading-relaxed">{{ d.intro }}</p>
                                <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-white">
                                    Explore
                                    <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                                </span>
                            </div>
                        </div>
                    </Reveal>
                </Link>
            </div>
        </div>
    </section>
</template>