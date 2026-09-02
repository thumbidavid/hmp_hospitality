<script setup>
import { ref, computed } from "vue"
import { Link, usePage, Head } from "@inertiajs/vue3"
import { ArrowRight, Clock, ArrowUpRight } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import SectionHead from "@/Components/Public/shared/SectionHead.vue"

const ARTICLES_HERO = "https://images.unsplash.com/photo-1481627834876-b7833e8f557f?auto=format&fit=crop&w=2000&q=80"

const page = usePage()

const active = ref("All")

// Dynamic categories mapped from database with standard fallback
const categoriesList = computed(() => {
    const list = page.props.categories && page.props.categories.length > 0
        ? page.props.categories
        : ["Property Stories", "Destination Insights", "Industry Intelligence", "Buyer Perspectives"]
    return ["All", ...list]
})

// Dynamic articles list computed with fallback mockup
const postsList = computed(() => {
    if (page.props.posts && page.props.posts.length > 0) {
        return page.props.posts
    }

    return [
        {
            id: "inside-conservation-model",
            slug: "inside-conservation-model",
            title: "Inside the Conservation Model of East Africa's Signature Lodges",
            excerpt: "Explore how premium independent hospitality businesses are partnering with local wildlife authorities and communities to protect critical ecological habitats.",
            image: "https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80",
            category: "Property Stories",
            read: "5 min",
            date: "May 12, 2026"
        },
        {
            id: "meeting-dynamic-specs",
            slug: "meeting-dynamic-specs",
            title: "Meeting Dynamic Programme Specs: A Corporate Buyer's Guide",
            image: "https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=400&q=80",
            category: "Buyer Perspectives",
            read: "3 min",
            date: "Apr 28, 2026"
        },
        {
            id: "connecting-business-events",
            slug: "connecting-business-events",
            title: "Connecting Business Events with Unique Venues",
            image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=400&q=80",
            category: "Destination Insights",
            read: "4 min",
            date: "Apr 15, 2026"
        },
        {
            id: "changing-landscape-advisory",
            slug: "changing-landscape-advisory",
            title: "The Changing Landscape of Commercial Advisory",
            image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=400&q=80",
            category: "Industry Intelligence",
            read: "3 min",
            date: "Mar 30, 2026"
        }
    ]
})

// Filter list based on selected category chip
const filtered = computed(() => {
    if (active.value === "All") return postsList.value
    return postsList.value.filter((a) => a.category === active.value)
})

// Segment the first post for prominent lead placement
const lead = computed(() => filtered.value[0])

// Segment remaining records for list rows (index 1+)
const rest = computed(() => filtered.value.slice(1))

defineOptions({
    layout: Layout
})
</script>

<template>
    <!-- Dynamic World-Class SEO Metadata Block -->

    <Head>
        <title>Stories & Insights</title>
        <meta name="description"
            content="Discover stories from our collection. Read expert editorial pieces on destinations, independent hotels, and business events across Africa." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Stories & Insights — HMP Hospitality" />
        <meta property="og:description"
            content="Expert hospitality insights, destination guides, and industry news represented across our curated portfolio." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1481627834876-b7833e8f557f?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[46vh] min-h-[20rem] flex items-end overflow-hidden">
            <img :src="ARTICLES_HERO" alt="Stories from our collection"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">Stories</span>
                <h1 class="mt-2 max-w-3xl font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">
                    Stories from Our Collection</h1>
                <p class="mt-4 max-w-2xl text-white/85 leading-relaxed">Editorial pieces on destinations, properties and
                    the business of hospitality across the markets we represent.</p>
            </div>
        </section>

        <!-- Sticky Filter bar -->
        <div class="sticky top-20 z-30 border-b border-border bg-background/85 backdrop-blur-xl">
            <div class="container-wide flex items-center gap-2 overflow-x-auto py-4 no-scrollbar">
                <button v-for="c in categoriesList" :key="c" @click="active = c"
                    :class="['chip whitespace-nowrap cursor-pointer', active === c ? 'chip-active' : '']">
                    {{ c }}
                </button>
            </div>
        </div>

        <!-- Grid Section -->
        <section class="container-wide py-16 lg:py-24">
            <div v-if="filtered.length === 0" class="py-20 text-center">
                <p class="text-muted-foreground">No stories in this category yet.</p>
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-2">
                <!-- Left Column (Lead Article Card) -->
                <Reveal v-if="lead">
                    <Link :href="`/stories/${lead.slug}`" class="group block h-full">
                        <div class="relative overflow-hidden rounded-2xl aspect-[16/10]">
                            <img :src="lead.image" :alt="lead.title" loading="lazy"
                                class="h-full w-full object-cover img-rise" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 to-transparent" />
                            <span
                                class="absolute left-5 top-5 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium text-ink shadow-sm">
                                {{ lead.category }}
                            </span>
                        </div>
                        <div class="mt-5">
                            <h3
                                class="font-heading text-2xl lg:text-3xl group-hover:text-primary transition-colors leading-tight">
                                {{ lead.title }}
                            </h3>
                            <p class="mt-3 text-muted-foreground leading-relaxed max-w-xl">
                                {{ lead.excerpt }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-3 text-xs text-muted-foreground">
                                <Clock class="h-3.5 w-3.5 text-primary" /> {{ lead.read }} read · {{ lead.date }}
                            </span>
                        </div>
                    </Link>
                </Reveal>

                <!-- Right Column (List of Secondary Articles) -->
                <div class="grid gap-6 content-start">
                    <Reveal v-for="(a, i) in rest" :key="a.id" :delay="i * 0.06">
                        <Link :href="`/stories/${a.slug}`"
                            class="group grid grid-cols-[7rem_1fr] sm:grid-cols-[9rem_1fr] gap-5 items-center">
                            <div class="relative overflow-hidden rounded-xl aspect-square">
                                <img :src="a.image" :alt="a.title" loading="lazy"
                                    class="h-full w-full object-cover img-rise" />
                            </div>
                            <div>
                                <span class="text-[11px] uppercase tracking-wider text-primary font-medium">
                                    {{ a.category }}
                                </span>
                                <h4
                                    class="mt-1.5 font-heading text-lg group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                    {{ a.title }}
                                </h4>
                                <span class="mt-2 inline-flex items-center gap-2 text-xs text-muted-foreground">
                                    <Clock class="h-3 w-3 text-primary" /> {{ a.read }} · {{ a.date }}
                                    <ArrowUpRight
                                        class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </span>
                            </div>
                        </Link>
                    </Reveal>
                </div>
            </div>
        </section>

        <!-- CTA Callout Block -->
        <section class="bg-ink text-white">
            <div class="container-wide py-20 lg:py-28 text-center">
                <SectionHead center light eyebrow="Plan with us" title="Turn a story into a programme"
                    sub="If a destination or property caught your eye, submit a brief and we will coordinate every member response." />
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <Link href="/rfp" class="btn-primary">
                        Submit an RFP
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link href="/portfolio" class="btn-light">
                        Explore the Portfolio
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>