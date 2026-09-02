<script setup>
import { computed } from "vue"
import { Head, Link, usePage } from "@inertiajs/vue3"
import { ArrowRight, ArrowUpRight, ChevronLeft, Check, Download, Plane, CalendarCheck } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import PropertyCard from "@/Components/Public/PortfolioCard.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import HmpAgencyCta from "@/Components/Public/HmpAgencyCta.vue"
import { useShortlistStore } from "@/Stores/shortlistStore"

const page = usePage()

const d = computed(() => page.props.destination)
const venues = computed(() => page.props.venues || [])

const shortlistStore = useShortlistStore()

// Computes if every venue in this destination is currently shortlisted
const allVenuesSelected = computed(() => {
    if (venues.value.length === 0) return false
    return venues.value.every(v => shortlistStore.has(v.id))
})

// Add/Remove all associated venues
const handleToggleAllVenues = () => {
    if (allVenuesSelected.value) {
        venues.value.forEach(v => {
            if (shortlistStore.has(v.id)) {
                shortlistStore.toggle({ id: v.id })
            }
        })
    } else {
        venues.value.forEach(v => {
            if (!shortlistStore.has(v.id)) {
                shortlistStore.toggle(v)
            }
        })
    }
}


defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>{{ d.name }} — {{ d.country }}</title>
        <meta name="description"
            :content="`Discover MICE travel, business events, and corporate incentive venues in ${d.name}, ${d.country}. ${d.intro}`" />
        <meta name="keywords"
            :content="`${d.name} travel, ${d.country} destinations, corporate incentives, MICE travel Africa`" />
        <meta property="og:title" :content="`${d.name}, ${d.country} — HMP Hospitality`" />
        <meta property="og:description" :content="d.intro" />
        <meta property="og:image" :content="d.image" />
        <meta property="og:type" content="article" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div v-if="d">
        <!-- Gallery Hero Banner -->
        <section class="relative h-[60vh] min-h-[26rem] flex items-end overflow-hidden">
            <img :src="d.image" :alt="d.name" class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />

            <div class="container-wide relative pt-28 pb-12 text-white">
                <Link href="/portfolio" class="inline-flex items-center gap-1.5 text-sm text-white/80 hover:text-white">
                    <ChevronLeft class="h-4 w-4" /> Destinations
                </Link>
                <span class="mt-6 block text-xs uppercase tracking-[0.28em] text-white/70">
                    {{ d.region }}
                </span>
                <h1 class="mt-2 font-heading text-5xl sm:text-6xl lg:text-7xl leading-none">
                    {{ d.name }}
                </h1>
                <p class="mt-3 text-white/80">
                    {{ d.country }}
                </p>
            </div>
        </section>

        <!-- Dynamic Introductions and highlights -->
        <section class="container-wide grid gap-12 lg:grid-cols-[1.5fr_1fr] py-16 lg:py-24">
            <!-- Left Info Block Column -->
            <div class="space-y-10">
                <Reveal>
                    <span class="eyebrow">Destination introduction</span>
                    <p class="mt-4 font-heading text-2xl leading-snug text-balance">
                        {{ d.intro }}
                    </p>
                </Reveal>

                <Reveal v-if="d.reasons && d.reasons.length > 0">
                    <h3 class="font-heading text-2xl mb-4">Reasons to meet or travel here</h3>
                    <ul class="grid gap-3 sm:grid-cols-2">
                        <li v-for="r in d.reasons" :key="r" class="flex items-start gap-2.5 text-sm text-foreground/80">
                            <Check class="h-4 w-4 mt-0.5 text-primary shrink-0" /> {{ r }}
                        </li>
                    </ul>
                </Reveal>

                <Reveal v-if="d.experiences && d.experiences.length > 0">
                    <h3 class="font-heading text-2xl mb-4">Experiences & attractions</h3>
                    <div class="flex flex-wrap gap-2.5">
                        <span v-for="e in d.experiences" :key="e" class="chip">
                            {{ e }}
                        </span>
                    </div>
                </Reveal>
            </div>

            <!-- Right Sidebar Column -->
            <aside class="space-y-6 lg:sticky lg:top-28 self-start">
                <div class="rounded-3xl border border-border bg-card p-6 space-y-5">
                    <div class="flex items-start gap-3">
                        <Plane class="h-5 w-5 text-primary mt-0.5" />
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground">Access & airline
                                connectivity</p>
                            <p class="mt-1 text-sm text-foreground/80 leading-relaxed">{{ d.access }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <CalendarCheck class="h-5 w-5 text-primary mt-0.5" />
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground">Best time to visit</p>
                            <p class="mt-1 text-sm text-foreground/80 leading-relaxed">{{ d.bestTime }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-border bg-card p-6">
                    <h3 class="font-heading text-xl">Planning a trip here?</h3>
                    <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                        Send us your brief and we'll match the right venues and ground partners.
                    </p>

                    <button @click="handleToggleAllVenues"
                        class="btn-primary mt-3 w-full justify-center cursor-pointer transition-colors">
                        {{ allVenuesSelected ? 'Remove All Venues' : 'Add All ' + d.name + ' Venues to Shortlist' }}
                    </button>

                    <div v-if="d.documents && d.documents.length > 0"
                        class="mt-5 pt-4 border-t border-border space-y-2">
                        <a v-for="doc in d.documents" :key="doc.id" :href="doc.url" target="_blank" rel="noreferrer"
                            class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors font-medium">
                            <Download class="h-4 w-4 text-primary" />Download {{ doc.label }} (PDF)
                        </a>
                    </div>
                </div>
            </aside>
        </section>

        <!-- Sister unit support banner -->
        <HmpAgencyCta />

        <!-- Represented Properties inside this Destination -->
        <section v-if="venues.length > 0" class="bg-sand/60">
            <div class="container-wide py-20">
                <Reveal class="mb-10">
                    <span class="eyebrow">Key venues & hotels</span>
                    <h2 class="mt-3 font-heading text-3xl sm:text-4xl">Stay here</h2>
                </Reveal>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Reveal v-for="(v, i) in venues" :key="v.id" :delay="i * 0.06" class="h-full">
                        <PropertyCard :it="v" />
                    </Reveal>
                </div>
            </div>
        </section>
    </div>
</template>