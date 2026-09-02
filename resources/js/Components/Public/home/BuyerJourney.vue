<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight, Compass, Layers, Send } from "lucide-vue-next"
import Reveal from "../shared/Reveal.vue"
import SectionHead from "../shared/SectionHead.vue"

const steps = [
    {
        icon: Compass,
        title: "Discover",
        desc: "Explore represented properties and destinations across the portfolio."
    },
    {
        icon: Layers,
        title: "Shortlist",
        desc: "Select the options that fit your programme and keep them in one shortlist."
    },
    {
        icon: Send,
        title: "Submit",
        desc: "Send one RFP and let HMP Hospitality coordinate the response."
    },
]
</script>

<template>
    <section class="bg-sand/40">
        <div class="container-wide py-20 lg:py-28">
            <!-- Global Section Header -->
            <SectionHead center eyebrow="The buyer journey" title="One Brief. The Right Connections."
                sub="From discovery to decision, a refined sourcing experience." />

            <!-- Journey Steps Grid -->
            <div class="mt-14 grid gap-8 lg:grid-cols-3">
                <Reveal v-for="(s, i) in steps" :key="s.title" :delay="i * 0.08">
                    <div class="h-full text-center">
                        <!-- Icon Circle container with number badge -->
                        <span
                            class="relative inline-grid place-items-center h-16 w-16 rounded-full bg-card border border-ink/10 text-primary">
                            <component :is="s.icon" class="h-6 w-6" />
                            <span
                                class="absolute -top-1 -right-1 grid place-items-center h-6 w-6 rounded-full bg-primary text-primary-foreground text-xs font-semibold">
                                {{ i + 1 }}
                            </span>
                        </span>

                        <h3 class="mt-6 font-heading text-2xl">
                            {{ s.title }}
                        </h3>

                        <p class="mt-3 text-sm text-muted-foreground leading-relaxed max-w-xs mx-auto">
                            {{ s.desc }}
                        </p>
                    </div>
                </Reveal>
            </div>

            <!-- Bottom Call to Action -->
            <Reveal class="mt-12 text-center">
                <Link href="/rfp" class="btn-primary">
                    Start Your RFP
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </Reveal>
        </div>
    </section>
</template>