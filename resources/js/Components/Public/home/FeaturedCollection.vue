<script setup>
import { computed } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import { ArrowRight, MapPin, Plus, Heart } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"
import SectionHead from "../shared/SectionHead.vue"
import { useShortlistStore } from "@/Stores/shortlistStore"

const page = usePage()
const shortlistStore = useShortlistStore()

const featuredList = computed(() => {
    if (page.props.featured && page.props.featured.length > 0) {
        return page.props.featured
    }

    // Fail-safe layout preview
    return [
        {
            id: 1,
            name: "HMP Safari Lodge",
            portfolioCategory: "Hotels, Resorts & Lodges",
            city: "Maasai Mara",
            country: "Kenya",
            image: "https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=800&q=80",
            blurb: "An award-winning ultra-luxury safari lodge situated on a dramatic bend overlooking the river and savannah plains."
        },
        {
            id: 2,
            name: "The Grand Conference Atrium",
            portfolioCategory: "Conference & Unique Venues",
            city: "Sandton",
            country: "South Africa",
            image: "https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80",
            blurb: "A state-of-the-art corporate event hub featuring massive skylights, variable partitions, and premium connectivity."
        },
        {
            id: 3,
            name: "Equator Serviced Apartments",
            portfolioCategory: "Serviced Residences",
            city: "Nairobi",
            country: "Kenya",
            image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
            blurb: "Elegant long-stay business suites tailored for international executives, project teams and luxury travellers."
        }
    ]
})

const isShortlisted = (property) => shortlistStore.has(property.id)

const handleToggleShortlist = (property) => {
    shortlistStore.toggle(property)
}
</script>

<template>
    <section class="container-wide py-20 lg:py-28">
        <!-- Header area -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <SectionHead eyebrow="Discover the Collection" title="A curated portfolio of independent places"
                sub="Hotels, resorts, lodges, conference and unique venues, serviced residences and destinations — each selected and represented." />
            <Reveal>
                <Link href="/portfolio" class="btn-ghost whitespace-nowrap">
                    View the Complete Portfolio
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </Reveal>
        </div>

        <!-- Collection Grid -->
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Reveal v-for="(p, i) in featuredList.slice(0, 6)" :key="p.id" :delay="(i % 3) * 0.06" class="h-full">
                <article class="group h-full flex flex-col">
                    <!-- Image Card with category tag -->
                    <Link :href="`/portfolio/${p.id}`" class="relative block overflow-hidden rounded-2xl aspect-[5/4]">
                        <img :src="p.image" :alt="p.name" loading="lazy" class="h-full w-full object-cover img-rise" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />
                        <span
                            class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium uppercase tracking-wider text-ink shadow-sm">
                            {{ p.portfolioCategory }}
                        </span>
                    </Link>

                    <!-- Content Details -->
                    <div class="flex flex-1 flex-col pt-5">
                        <span class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <MapPin class="h-3.5 w-3.5 text-primary" /> {{ p.city }}, {{ p.country }}
                        </span>

                        <h3 class="mt-2 font-heading text-xl lg:text-2xl">
                            <Link :href="`/portfolio/${p.id}`" class="hover:text-primary transition-colors">
                                {{ p.name }}
                            </Link>
                        </h3>

                        <p class="mt-2 text-sm text-muted-foreground leading-relaxed line-clamp-2">
                            {{ p.blurb }}
                        </p>

                        <!-- Bottom Action Menu -->
                        <div class="mt-5 flex items-center justify-between pt-4 border-t border-border mt-auto">
                            <Link :href="`/portfolio/${p.id}`" class="editorial-link">
                                Explore
                                <ArrowRight class="h-4 w-4 arr" />
                            </Link>
                            <button @click="handleToggleShortlist(p)" :class="[
                                'inline-flex items-center gap-1.5 text-sm font-medium transition-colors cursor-pointer',
                                isShortlisted(p) ? 'text-primary' : 'text-foreground/75 hover:text-primary'
                            ]">
                                <Heart class="h-4 w-4" :class="{ 'fill-current': isShortlisted(p) }" />
                                {{ isShortlisted(p) ? 'In Shortlist' : 'Add to Shortlist' }}
                            </button>
                        </div>
                    </div>
                </article>
            </Reveal>
        </div>
    </section>
</template>
