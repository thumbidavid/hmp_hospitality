<script setup>
import { ref, computed, watch } from "vue"
import { Head, Link, usePage, router } from "@inertiajs/vue3"
import { Search, SlidersHorizontal, X } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import PortfolioCard from "@/Components/Public/PortfolioCard.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

// Restored Destinations & DMOs tab
const categories = [
    { v: "All", l: "All" },
    { v: "Hotels, Resorts & Lodges", l: "Hotels, Resorts & Lodges" },
    { v: "Conference & Unique Venues", l: "Conference & Unique Venues" },
    { v: "Serviced Residences", l: "Serviced Residences" },
    { v: "Destinations & DMOs", l: "Destinations & DMOs" },
]

const sortOptions = [
    { v: "featured", l: "Featured" },
    { v: "name", l: "Alphabetical" },
    { v: "location", l: "Location" }
]

const propertyTypes = ["Hotel", "Resort", "Safari Lodge/Camp", "Conference Venue", "Unique Venue", "Serviced Residence"]
const PORTFOLIO_HERO = "https://images.unsplash.com/photo-1610642372684-6d5c0a1ab0a3?auto=format&fit=crop&w=2000&q=80"

const page = usePage()

// Track local filters
const q = ref(page.props.requestFilters.q)
const cat = ref(page.props.requestFilters.cat)
const region = ref(page.props.requestFilters.region)
const country = ref(page.props.requestFilters.country)
const city = ref(page.props.requestFilters.city)
const type = ref(page.props.requestFilters.type)
const coll = ref(page.props.requestFilters.coll)
const minRooms = ref(page.props.requestFilters.minRooms)
const minCap = ref(page.props.requestFilters.minCap)
const featured = ref(page.props.requestFilters.featured)
const sort = ref(page.props.requestFilters.sort)

const showFilters = ref(false)

const activeCount = computed(() => {
    return [
        q.value, region.value, country.value, city.value,
        type.value, coll.value, minRooms.value, minCap.value,
        featured.value && "f"
    ].filter(Boolean).length
})

const applyFilters = () => {
    router.get('/portfolio', {
        q: q.value,
        cat: cat.value,
        region: region.value,
        country: country.value,
        city: city.value,
        type: type.value,
        coll: coll.value,
        minRooms: minRooms.value,
        minCap: minCap.value,
        featured: featured.value ? '1' : '0',
        sort: sort.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}

watch([cat, country, type, sort, region, city, coll, minRooms, minCap, featured], () => {
    applyFilters()
})

const clearAll = () => {
    q.value = ""
    region.value = ""
    country.value = ""
    city.value = ""
    type.value = ""
    coll.value = ""
    minRooms.value = ""
    minCap.value = ""
    featured.value = false
    applyFilters()
}

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>Explore Our Collection</title>
        <meta name="description"
            content="Browse our curated portfolio of independent hotels, luxury resorts, safari lodges, serviced residences, and unique meeting venues across global markets." />
        <meta name="keywords"
            content="MICE venues, luxury resorts, boutique hotels, serviced residences, African destinations, luxury travel representation" />
        <meta property="og:title" content="Explore Our Collection — HMP Hospitality" />
        <meta property="og:description"
            content="Discover represented hotels, unique venues, and serviced residences tailored for business travel, incentives, and corporate events." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1610642372684-6d5c0a1ab0a3?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[52vh] min-h-[22rem] flex items-end overflow-hidden">
            <img :src="PORTFOLIO_HERO" alt="Explore our portfolio"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">The collection</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">Explore Our
                    Portfolio</h1>
                <p class="mt-4 max-w-2xl text-white/85 leading-relaxed">Discover a curated collection of independent
                    hotels, venues, serviced residences and destinations.</p>
            </div>
        </section>

        <!-- Category tabs -->
        <section class="container-wide pt-8 pb-6">
            <div class="flex flex-wrap gap-2.5">
                <button v-for="c in categories" :key="c.v" @click="cat = c.v" :class="[
                    'rounded-full px-5 py-2.5 text-sm font-medium transition-all border cursor-pointer',
                    cat === c.v ? 'bg-primary text-primary-foreground border-primary' : 'border-border text-foreground/75 hover:border-foreground/40'
                ]">
                    {{ c.l }}
                </button>
            </div>
        </section>

        <!-- Toolbar -->
        <div class="sticky top-20 z-30 border-y border-border bg-background/85 backdrop-blur-xl">
            <div class="container-wide py-3 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-56">
                    <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <input v-model="q" @input="applyFilters" placeholder="Search the collection"
                        class="input-field !pl-10 !py-2.5" />
                </div>

                <select v-model="country" class="input-field !py-2.5 !w-auto cursor-pointer">
                    <option value="">All countries</option>
                    <option v-for="c in page.props.filters.countries" :key="c" :value="c">{{ c }}</option>
                </select>

                <select v-model="type" class="input-field !py-2.5 !w-auto hidden sm:block cursor-pointer">
                    <option value="">All types</option>
                    <option v-for="c in propertyTypes" :key="c" :value="c">{{ c }}</option>
                </select>

                <button @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 rounded-full border border-border px-4 py-2.5 text-sm hover:border-primary cursor-pointer">
                    <SlidersHorizontal class="h-4 w-4" /> More filters
                    <span v-if="activeCount > 0"
                        class="grid place-items-center h-5 w-5 rounded-full bg-primary text-primary-foreground text-[11px]">
                        {{ activeCount }}
                    </span>
                </button>

                <div class="ml-auto">
                    <select v-model="sort" class="input-field !py-2.5 !w-auto cursor-pointer">
                        <option v-for="o in sortOptions" :key="o.v" :value="o.v">Sort: {{ o.l }}</option>
                    </select>
                </div>
            </div>

            <!-- Collapsible Filters -->
            <div v-if="showFilters" class="container-wide pb-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-5">
                <select v-model="region" class="input-field !py-2.5 cursor-pointer">
                    <option value="">All regions</option>
                    <option v-for="c in page.props.filters.regions" :key="c" :value="c">{{ c }}</option>
                </select>

                <select v-model="city" class="input-field !py-2.5 cursor-pointer">
                    <option value="">All cities</option>
                    <option v-for="c in page.props.filters.cities" :key="c" :value="c">{{ c }}</option>
                </select>

                <select v-model="coll" class="input-field !py-2.5 cursor-pointer">
                    <option value="">Any setting</option>
                    <option v-for="c in page.props.filters.settings" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>

                <input v-model="minRooms" type="number" min="0" placeholder="Min. guestrooms"
                    class="input-field !py-2.5" />
                <input v-model="minCap" type="number" min="0" placeholder="Min. meeting capacity"
                    class="input-field !py-2.5" />

                <label class="flex items-center gap-2 text-sm lg:col-span-1 cursor-pointer">
                    <input type="checkbox" v-model="featured" class="h-4 w-4 accent-[hsl(var(--primary))]" />
                    Featured only
                </label>

                <button @click="clearAll"
                    class="inline-flex items-center justify-center gap-1.5 text-sm text-muted-foreground hover:text-primary cursor-pointer">
                    <X class="h-4 w-4" /> Clear all filters
                </button>
            </div>
        </div>

        <!-- Portfolio Items Grid -->
        <section class="container-wide py-10">
            <div class="mb-6 flex items-center justify-between text-sm text-muted-foreground">
                <span>
                    <strong class="text-foreground">{{ page.props.items.length }}</strong>
                    {{ page.props.items.length === 1 ? 'result' : 'results' }}
                    <span v-if="cat !== 'All'"> in {{ cat }}</span>
                </span>
                <button v-if="activeCount > 0" @click="clearAll"
                    class="text-primary hover:underline cursor-pointer">Reset</button>
            </div>

            <!-- No results visual -->
            <div v-if="page.props.items.length === 0"
                class="rounded-3xl border border-border bg-card py-20 text-center">
                <p class="font-heading text-2xl">No exact matches for your search.</p>
                <p class="mt-3 text-muted-foreground">Clear a filter, or let HMP Hospitality recommend suitable options.
                </p>
                <div class="mt-6 flex flex-wrap gap-3 justify-center">
                    <button @click="clearAll" class="btn-ghost">Clear filters</button>
                    <Link href="/rfp" class="btn-primary">Ask HMP to recommend</Link>
                </div>
            </div>

            <!-- Results Grid -->
            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Reveal v-for="(it, i) in page.props.items" :key="`${it.category}-${it.id}`" :delay="i * 0.04"
                    class="h-full">
                    <PortfolioCard :it="it" />
                </Reveal>
            </div>
        </section>
    </div>
</template>