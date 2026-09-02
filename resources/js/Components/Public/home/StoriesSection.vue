<script setup>
import { computed } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import { ArrowUpRight, Clock, ArrowRight } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"
import SectionHead from "../shared/SectionHead.vue"

const page = usePage()

// Base Taxonomy Categories computed with fallback
const categoryList = computed(() => {
    if (page.props.blogCategories && page.props.blogCategories.length > 0) {
        return page.props.blogCategories
    }
    return ["Property Stories", "Destination Insights", "Industry Intelligence", "Buyer Perspectives"]
})

// Dynamic Blog list computed with fallback mockup
const blogPostsList = computed(() => {
    if (page.props.blogPosts && page.props.blogPosts.length > 0) {
        return page.props.blogPosts
    }

    return [
        {
            id: 1,
            title: "Inside the Conservation Model of East Africa's Signature Lodges",
            slug: "inside-conservation-model",
            excerpt: "Explore how premium independent hospitality businesses are partnering with local wildlife authorities and communities to protect critical ecological habitats.",
            image: "https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80",
            category: "Property Stories",
            read: "5 min",
            date: "May 12, 2026"
        },
        {
            id: 2,
            title: "Meeting Dynamic Programme Specs: A Corporate Buyer's Guide",
            slug: "meeting-dynamic-specs",
            image: "https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=400&q=80",
            category: "Buyer Perspectives",
            read: "3 min",
            date: "Apr 28, 2026"
        },
        {
            id: 3,
            title: "Connecting Business Events with Unique Venues",
            slug: "connecting-business-events",
            image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=400&q=80",
            category: "Destination Insights",
            read: "4 min",
            date: "Apr 15, 2026"
        },
        {
            id: 4,
            title: "The Changing Landscape of Commercial Advisory",
            slug: "changing-landscape-advisory",
            image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=400&q=80",
            category: "Industry Intelligence",
            read: "3 min",
            date: "Mar 30, 2026"
        }
    ]
})

// Extract the 1st article as the prominent visual banner
const lead = computed(() => blogPostsList.value[0])

// Extract all subsequent articles (indices 1-3)
const rest = computed(() => blogPostsList.value.slice(1))
</script>

<template>
    <section class="bg-sand/40">
        <div class="container-wide py-20 lg:py-28">
            <!-- Header section & Dynamic View All button -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                <SectionHead eyebrow="Stories from Our Collection"
                    title="Insights, destinations and the business of hospitality"
                    sub="Editorial pieces from across the HMP Hospitality portfolio." />
                <Reveal>
                    <!-- Triggers transition link to stories index page -->
                    <Link href="#" class="btn-ghost whitespace-nowrap">
                        View All Stories
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </Reveal>
            </div>

            <!-- Category Chip Selector -->
            <div class="mt-6 flex flex-wrap gap-2">
                <span v-for="c in categoryList" :key="c" class="chip">
                    {{ c }}
                </span>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                <!-- Left Column (Lead Article Card) -->
                <Reveal v-if="lead">
                    <Link :href="`/stories/${lead.slug}`" class="group block h-full">
                        <div class="relative overflow-hidden rounded-2xl aspect-[16/10]">
                            <img :src="lead.image" :alt="lead.title" loading="lazy"
                                class="h-full w-full object-cover img-rise" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 to-transparent" />
                            <span
                                class="absolute left-5 top-5 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium text-ink">
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
                <div class="grid gap-6">
                    <Reveal v-for="(a, i) in rest" :key="a.id" :delay="i * 0.06">
                        <Link :href="`/stories/${a.slug}`"
                            class="group grid grid-cols-[7rem_1fr] sm:grid-cols-[9rem_1fr] gap-5 items-center">
                            <!-- Image card -->
                            <div class="relative overflow-hidden rounded-xl aspect-square">
                                <img :src="a.image" :alt="a.title" loading="lazy"
                                    class="h-full w-full object-cover img-rise" />
                            </div>

                            <!-- Metadata and Title -->
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
        </div>
    </section>
</template>