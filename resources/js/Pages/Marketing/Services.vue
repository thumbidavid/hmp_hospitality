<script setup>
import { computed } from "vue"
import { Head, Link } from "@inertiajs/vue3"
import { ArrowRight, Compass } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import SectionHead from "@/Components/Public/shared/SectionHead.vue"

const SERVICES_HERO = "https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=2000&q=80"

// Member-focused representation service stacks
const serviceGroups = computed(() => [
    {
        id: "representation", title: "Strategic Sales & Representation",
        items: [
            { title: "Targeted Buyer & Agency Introductions", desc: "Active outreach to qualified travel advisors, tour operators, TMCs, corporate buyers and meeting planners." },
            { title: "Sales Calls & Sales Missions", desc: "Planned sales calls and multi-market sales missions into priority source markets." },
            { title: "Account Development", desc: "Always-on management of your most valuable buyer relationships and accounts." },
            { title: "Corporate & MICE Market Representation", desc: "A dedicated commercial voice in the corporate, MICE and association segments." },
            { title: "Travel Advisor & Tour Operator Engagement", desc: "Programmed engagement with the trade that books your property or destination." },
            { title: "Consortia & Agency Programme Support", desc: "Support across consortia, agency programmes and contracting channels." },
            { title: "Contracting Introductions", desc: "Warm, qualified introductions that lead to rate and programme contracts." },
            { title: "Lead Qualification & Follow-up", desc: "Every lead qualified, routed and followed up against agreed timelines." },
            { title: "Market Intelligence", desc: "Buyer, rate and demand insight shared back with represented members." },
            { title: "Property & Destination Training", desc: "We train buyers and the trade so they sell your story accurately." },
        ],
    },
    {
        id: "brand", title: "Brand Awareness & Marketing",
        items: [
            { title: "Brand Positioning & Messaging", desc: "Clear, consistent positioning for independent hospitality and destinations." },
            { title: "Sales Collateral Support", desc: "Fact sheets, presentations and proposals that present members at their best." },
            { title: "Public Relations", desc: "Coordinated PR that places your story with the right audiences." },
            { title: "Social Media Visibility", desc: "Curated social presence across the channels buyers and advisors use." },
            { title: "Buyer Communications", desc: "Regular, relevant communication with active and prospective buyers." },
            { title: "Monthly Advisor & Buyer E-Magazines", desc: "Featured placement in HMP's monthly trade and buyer e-magazines." },
            { title: "Joint Marketing Campaigns", desc: "Co-funded campaigns aligned to your market and season priorities." },
            { title: "Property & Destination Features", desc: "Editorial features that bring your story to life for buyers." },
        ],
    },
    {
        id: "events", title: "Industry Events & Engagement",
        items: [
            { title: "Trade Show Representation", desc: "We represent you at the shows that matter to your market." },
            { title: "Sales Roadshows", desc: "Multi-city roadshows that put your property in front of concentrated buyers." },
            { title: "Buyer Networking Events", desc: "Hosted buyer events that build qualified, face-to-face relationships." },
            { title: "Familiarisation Trips", desc: "Curated fam trips that convert advisors into confident sellers." },
            { title: "Hosted Buyer Programmes", desc: "Invited buyer programmes built around your target segments." },
            { title: "Curated Industry Partnerships", desc: "Strategic partnerships that extend reach into priority audiences." },
            { title: "Property Showcases", desc: "Targeted showcase dinners and presentations for priority buyers." },
        ],
    },
    {
        id: "advisory", title: "Commercial Advisory",
        items: [
            { title: "Sales Training", desc: "Practical, results-focused training for your commercial team." },
            { title: "Sales Strategy", desc: "A clear commercial roadmap and the positioning to win your market." },
            { title: "Revenue Consultancy", desc: "Pricing, forecasting and distribution built around your goals." },
            { title: "Commercial Performance Advisory", desc: "Ongoing review of pipeline, conversion and commercial outcomes." },
            { title: "Hotel Sales Outsourcing", desc: "We run your sales desk end to end, as a seamless extension of your team." },
            { title: "Market-Entry Support", desc: "Structured entry into new source markets and segments." },
        ],
    },
])

// Buyer-focused sourcing service stacks
const servicesForBuyers = computed(() => [
    {
        id: "sourcing",
        title: "Hotel, Resort & Lodge Sourcing",
        desc: "Tell us your brief; we surface the right represented properties for it.",
        benefits: [
            "Curated shortlist",
            "Fit-checked options",
            "Time saved"
        ],
        ideal: "Buyers sourcing across Africa.",
        cta: "Submit an RFP"
    },
    {
        id: "venue-sourcing",
        title: "Venue Sourcing",
        desc: "Conference, convention and unique venues matched to your programme.",
        benefits: [
            "Capacity-matched",
            "Like-for-like options",
            "Venue-only enquiries"
        ],
        ideal: "Venue-only or venue-led events."
    },
    {
        id: "residences",
        title: "Serviced Residence Sourcing",
        desc: "Extended-stay residences for project teams and long-stay delegates.",
        benefits: [
            "Long-stay fit",
            "Corporate suitability",
            "Project-stay support"
        ],
        ideal: "Long-stay and project programmes."
    },
    { id: "destination-sourcing", title: "Destination Sourcing", desc: "Compare represented destinations against your brief, from access to experience.", benefits: ["Destination fit review", "Access & seasonality", "Itinerary input"], ideal: "Destinations not yet decided." },
    { id: "group-accommodation", title: "Group Accommodation", desc: "Coordinated room blocks and rate negotiation across represented members.", benefits: ["Block management", "Rate negotiation", "Contracting"], ideal: "Groups and delegations." },
    { id: "rfp-coordination", title: "RFP Coordination", desc: "We brief members, chase proposals and present them to you.", benefits: ["Brief to members", "Proposal chasing", "Decision support"], ideal: "Busy planning teams." },
    { id: "shortlisting", title: "Property & Venue Shortlisting", desc: "Build a shortlist and submit one RFP to all of them.", benefits: ["Side-by-side compare", "Saved shortlist", "One RFP to many"], ideal: "Comparing 2–10 options." },
    { id: "site-inspection", title: "Site Inspection Coordination", desc: "We arrange site visits and join you where it counts.", benefits: ["Itinerary planning", "Property access", "On-site support"], ideal: "Shortlisted properties." },
])

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>Representation & Strategy Services</title>
        <meta name="description"
            content="Bespoke sales representation, commercial advisory, distribution strategies, and comprehensive property sourcing services for independent hotels and buyers." />
        <meta name="keywords"
            content="hotel distribution advice, sales representation, property sourcing, yield management strategy" />
        <meta property="og:title" content="Our Services — HMP Hospitality" />
        <meta property="og:description"
            content="Outsourced reach, active buyer relations, and yield advisory connecting outstanding properties with high-yield global booking channels." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[52vh] min-h-[22rem] flex items-end overflow-hidden">
            <img :src="SERVICES_HERO" alt="Representation that creates opportunity"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">Services</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">Representation
                    That Creates Opportunity</h1>
                <p class="mt-4 max-w-2xl text-white/85 leading-relaxed">We extend the reach of independent hospitality
                    businesses through strategic sales, marketing and market engagement.</p>
            </div>
        </section>

        <!-- Member services -->
        <section class="container-wide py-16 lg:py-24 space-y-16">
            <Reveal class="max-w-2xl">
                <p class="text-muted-foreground leading-relaxed">
                    For independent hotels, resorts, lodges, venues, serviced residences and destinations seeking
                    representation — a complete commercial stack delivered as an extension of your team.
                </p>
            </Reveal>

            <!-- Loop Group Categories -->
            <div v-for="(g, gi) in serviceGroups" :key="g.id">
                <Reveal>
                    <h2 class="font-heading text-2xl sm:text-3xl mb-6 flex items-center gap-3">
                        <span
                            class="grid place-items-center h-9 w-9 rounded-full bg-primary/10 text-primary text-sm font-semibold">
                            {{ gi + 1 }}
                        </span>
                        {{ g.title }}
                    </h2>
                </Reveal>

                <!-- Sub-items Grid with hover transform transitions -->
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Reveal v-for="(it, i) in g.items" :key="it.title" :delay="(i % 3) * 0.05">
                        <div
                            class="h-full rounded-2xl border border-border bg-card p-6 transition-all duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-[0_30px_60px_-35px_rgba(20,14,8,0.25)]">
                            <h3 class="font-heading text-lg">
                                {{ it.title }}
                            </h3>
                            <p class="mt-2 text-sm text-muted-foreground leading-relaxed">
                                {{ it.desc }}
                            </p>
                        </div>
                    </Reveal>
                </div>
            </div>

            <Reveal>
                <Link href="/partners" class="btn-primary">
                    Join the Portfolio
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </Reveal>
        </section>

        <!-- Reusable HMP Agency CTA (Shared layout frame) -->
        <section class="bg-sand/60">
            <div class="container-wide py-16 lg:py-20">
                <Reveal class="rounded-3xl border border-border bg-card p-8 lg:p-12">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                        <span
                            class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary shrink-0">
                            <Compass class="h-5 w-5" />
                        </span>
                        <div class="flex-1">
                            <h3 class="font-heading text-2xl text-balance">Planning beyond the property?</h3>
                            <p class="mt-2 text-muted-foreground leading-relaxed max-w-2xl">
                                For programmes requiring travel, destination logistics or complete event delivery, our
                                sister business unit, HMP Agency, provides coordinated support from arrival to
                                departure.
                            </p>
                        </div>
                        <Link href="/hmp-agency" class="btn-primary whitespace-nowrap">
                            Explore HMP Agency
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- Buyer services Section -->
        <section class="container-wide py-16 lg:py-24">
            <SectionHead eyebrow="For buyers & meeting planners" title="Find the right place, without the runaround"
                sub="One brief, a curated shortlist, coordinated proposals — and a team to carry it through." />

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Reveal v-for="(s, i) in servicesForBuyers" :key="s.id" :delay="(i % 3) * 0.05">
                    <div
                        class="group h-full rounded-3xl border border-border bg-card p-7 transition-all duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-[0_30px_60px_-35px_rgba(20,14,8,0.25)]">
                        <h3 class="font-heading text-xl">
                            {{ s.title }}
                        </h3>
                        <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                            {{ s.desc }}
                        </p>

                        <!-- List bullet indicators if benefits exist -->
                        <div v-if="s.benefits" class="mt-5 space-y-2">
                            <p v-for="b in s.benefits" :key="b"
                                class="text-sm text-foreground/75 flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 rounded-full bg-primary shrink-0" />
                                {{ b }}
                            </p>
                        </div>

                        <Link href="/rfp" class="editorial-link mt-5">
                            {{ s.cta || "Submit an RFP" }}
                            <ArrowRight class="h-4 w-4 arr" />
                        </Link>
                    </div>
                </Reveal>
            </div>

            <Reveal class="mt-10">
                <Link href="/rfp" class="btn-primary">
                    Submit an RFP
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </Reveal>
        </section>
    </div>
</template>