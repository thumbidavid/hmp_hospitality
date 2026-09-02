<script setup>
import { Head, Link } from "@inertiajs/vue3"
import { Target, Eye, ArrowRight, Handshake, ShieldCheck, Layers, Users, Building2, Compass } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import SectionHead from "@/Components/Public/shared/SectionHead.vue"

const ABOUT_HERO = "https://images.unsplash.com/photo-1551882547-ff40ac63a5e1?auto=format&fit=crop&w=2000&q=80"

const stats = [
    { k: "12+", v: "destinations represented" },
    { k: "40+", v: "qualified buyers engaged monthly" },
    { k: "4", v: "portfolio collections" },
    { k: "+38%", v: "avg. group pipeline lift" }
]

const buyerSupport = [
    "Curated, fit-checked shortlists",
    "One RFP to multiple represented members",
    "Coordinated, like-for-like proposals",
    "Site inspection support",
    "Warm venue and destination introductions",
    "A single, accountable partner"
]

const memberSupport = [
    "Dedicated market representation",
    "Outsourced sales desk option",
    "Strategy, training & marketing",
    "Buyer events and trade shows",
    "Revenue and distribution guidance",
    "Measurable, reported pipeline"
]

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>About Us</title>
        <meta name="description"
            content="Discover HMP Hospitality's mission, vision, and leadership. We provide bespoke sales and marketing representation for independent hospitality businesses." />
        <meta name="keywords"
            content="hotel sales desk, luxury representation, Africa travel representation, commercial strategy" />
        <meta property="og:title" content="About Us — HMP Hospitality" />
        <meta property="og:description"
            content="We connect outstanding independent hotels, resorts, venues, and destinations with international buyers and qualified travel professionals." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1551882547-ff40ac63a5e1?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[52vh] min-h-[22rem] flex items-end overflow-hidden">
            <img :src="ABOUT_HERO" alt="Connecting independent hospitality with global markets"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">About HMP Hospitality</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">Connecting
                    Independent Hospitality with Global Markets</h1>
            </div>
        </section>

        <!-- Intro Copy -->
        <section class="container-wide py-12 lg:py-20">
            <Reveal class="max-w-3xl">
                <p class="text-lg text-foreground/85 leading-relaxed">
                    HMP Hospitality provides bespoke sales and marketing representation for independent hotels, resorts,
                    lodges, venues, serviced residences and destinations. We connect our represented members with travel
                    advisors, agencies, corporate buyers, meeting planners and industry partners across priority
                    regional and international markets.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <Link href="/partners" class="btn-primary">Join the Portfolio</Link>
                    <Link href="/portfolio" class="btn-ghost">Explore the Portfolio</Link>
                </div>
            </Reveal>
        </section>

        <!-- Key Metrics Stats Row -->
        <section
            class="container-wide grid gap-px sm:grid-cols-2 lg:grid-cols-4 rounded-3xl border border-border bg-border overflow-hidden">
            <Reveal v-for="s in stats" :key="s.v" class="bg-card p-8">
                <p class="font-heading text-4xl text-primary">{{ s.k }}</p>
                <p class="mt-2 text-sm text-muted-foreground">{{ s.v }}</p>
            </Reveal>
        </section>

        <!-- Mission / Vision -->
        <section class="container-wide grid gap-10 lg:grid-cols-2 py-20 lg:py-28">
            <Reveal>
                <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                    <Target class="h-5 w-5" />
                </span>
                <h2 class="mt-6 font-heading text-3xl">Our Mission</h2>
                <p class="mt-4 text-muted-foreground leading-relaxed">
                    To connect exceptional independent hospitality businesses and destinations with the markets, buyers
                    and opportunities that support sustainable commercial growth.
                </p>
            </Reveal>

            <Reveal :delay="0.08">
                <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                    <Eye class="h-5 w-5" />
                </span>
                <h2 class="mt-6 font-heading text-3xl">Our Vision</h2>
                <p class="mt-4 text-muted-foreground leading-relaxed">
                    To become Africa's most trusted hospitality representation network, connecting independent
                    hospitality with global markets.
                </p>
            </Reveal>
        </section>

        <!-- Value Proposition Grid -->
        <section class="bg-sand/60">
            <div class="container-wide py-20 lg:py-28">
                <SectionHead eyebrow="Value proposition"
                    title="Commercial leadership, buyer access and a measured approach to growth"
                    sub="We sit between remarkable places and the people who book them — and we make the connection work for both sides." />
                <div class="grid gap-6 md:grid-cols-4">
                    <Reveal v-for="([t, d], i) in [
                        ['Outsourced reach', 'Representation without the overhead of a full in-house team.'],
                        ['Buyer relationships', 'Active, qualified access to planners and buyers across source markets.'],
                        ['Strategy & training', 'A path to stronger sales, sharper positioning and better teams.'],
                        ['Measurable pipeline', 'We report on what we build — pipeline, conversion and outcomes.']
                    ]" :key="t" :delay="i * 0.06">
                        <div class="h-full rounded-3xl border border-border bg-card p-7">
                            <h3 class="font-heading text-xl">{{ t }}</h3>
                            <p class="mt-3 text-sm text-muted-foreground leading-relaxed">{{ d }}</p>
                        </div>
                    </Reveal>
                </div>
            </div>
        </section>

        <!-- Strategic Business Units -->
        <section class="container-wide py-20 lg:py-28">
            <SectionHead eyebrow="Our business units" title="Two specialist teams, one connected platform"
                sub="HMP Hospitality and HMP Agency are specialist business units of Hotel and Meeting Planner Ltd." />
            <div class="grid gap-6 lg:grid-cols-2">
                <Reveal>
                    <div class="h-full rounded-3xl border border-border bg-card p-8">
                        <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <Building2 class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 font-heading text-2xl">HMP Hospitality</h3>
                        <p class="mt-3 text-muted-foreground leading-relaxed">
                            Bespoke sales and marketing representation connecting independent hotels, venues, serviced
                            residences and destinations with qualified global buyers.
                        </p>
                        <Link href="/portfolio" class="btn-primary mt-6">
                            Explore HMP Hospitality
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>

                <Reveal :delay="0.08">
                    <div class="h-full rounded-3xl border border-border bg-card p-8">
                        <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <Compass class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 font-heading text-2xl">HMP Agency</h3>
                        <p class="mt-3 text-muted-foreground leading-relaxed">
                            Business travel, destination management, meetings and events services for organisations
                            planning programmes in Kenya and across Africa.
                        </p>
                        <Link href="/hmp-agency" class="btn-ghost mt-6">
                            Discover HMP Agency
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- Leadership Bio Rows -->
        <section class="container-wide py-12 lg:py-20">
            <SectionHead eyebrow="Leadership" title="Built by hospitality people, for hospitality businesses" />
            <div class="grid gap-8 md:grid-cols-2">
                <Reveal v-for="([role, bio], i) in [
                    ['Founder & Managing Director', 'Two decades across hotel sales, revenue and representation across East and Southern Africa.'],
                    ['Head of Buyer Relations', 'A meetings-and-events background spanning corporate, association and incentive segments.']
                ]" :key="role" :delay="i * 0.08">
                    <div class="flex gap-6 rounded-3xl border border-border bg-card p-7">
                        <div
                            class="grid place-items-center h-20 w-20 shrink-0 rounded-full bg-primary/10 text-primary font-heading text-2xl">
                            {{ i === 0 ? 'H' : 'B' }}
                        </div>
                        <div>
                            <h3 class="font-heading text-xl">{{ role }}</h3>
                            <p class="mt-3 text-sm text-muted-foreground leading-relaxed">{{ bio }}</p>
                        </div>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- How we support Lists -->
        <section class="bg-sand/60">
            <div class="container-wide py-20 lg:py-28 grid gap-10 lg:grid-cols-2">
                <Reveal>
                    <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                        <Users class="h-5 w-5" />
                    </span>
                    <h2 class="mt-6 font-heading text-3xl">How we support buyers</h2>
                    <ul class="mt-5 space-y-3">
                        <li v-for="b in buyerSupport" :key="b"
                            class="flex items-start gap-2.5 text-sm text-foreground/80">
                            <ShieldCheck class="h-4 w-4 mt-0.5 text-primary shrink-0" /> {{ b }}
                        </li>
                    </ul>
                </Reveal>

                <Reveal :delay="0.08">
                    <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                        <Layers class="h-5 w-5" />
                    </span>
                    <h2 class="mt-6 font-heading text-3xl">How we support members</h2>
                    <ul class="mt-5 space-y-3">
                        <li v-for="m in memberSupport" :key="m"
                            class="flex items-start gap-2.5 text-sm text-foreground/80">
                            <Handshake class="h-4 w-4 mt-0.5 text-primary shrink-0" /> {{ m }}
                        </li>
                    </ul>
                </Reveal>
            </div>
        </section>

        <!-- Affiliation ecosystems -->
        <section class="container-wide py-20 lg:py-28">
            <Reveal class="rounded-3xl bg-ink p-10 lg:p-16 text-center">
                <span class="eyebrow !text-primary">Industry reach</span>
                <h2 class="mx-auto mt-3 max-w-2xl font-heading text-3xl sm:text-4xl text-white text-balance">
                    Affiliated across the meetings, business travel and hospitality ecosystem.
                </h2>
                <p class="mt-5 text-ink-fg/70">
                    MPI · SITE · Skal · Africa Travel Association · local tourism boards and destination partners.
                </p>
                <Link href="/partners" class="btn-primary mt-8">Meet the partners
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </Reveal>
        </section>
    </div>
</template>