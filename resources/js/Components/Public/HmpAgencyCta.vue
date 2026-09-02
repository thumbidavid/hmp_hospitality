<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowRight, Compass } from "lucide-vue-next"
import Reveal from "@/Components/Public/shared/Reveal.vue"
</script>

<template>
    <section class="container-wide pb-20 lg:pb-28">
        <Reveal class="rounded-3xl border border-border bg-sand/40 p-8 lg:p-12">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary shrink-0">
                    <Compass class="h-5 w-5" />
                </span>
                <div class="flex-1">
                    <h3 class="font-heading text-2xl text-balance">Need complete programme support?</h3>
                    <p class="mt-2 text-muted-foreground leading-relaxed max-w-2xl">
                        Beyond property sourcing, our sister business unit, HMP Agency, can support your business
                        travel, destination logistics, meetings and complete event delivery.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 lg:flex-col lg:w-auto">
                    <Link href="/hmp-agency" class="btn-primary whitespace-nowrap">
                        Explore HMP Agency Services
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link href="/hmp-agency#service-request" class="btn-ghost whitespace-nowrap">
                        Request Additional Support
                    </Link>
                </div>
            </div>
        </Reveal>
    </section>
</template>