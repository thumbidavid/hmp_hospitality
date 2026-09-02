<script setup>
import { Link, Head } from "@inertiajs/vue3"
import { Check, ArrowRight } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"

defineProps({
    email: String
})

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>Unsubscribed</title>
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <div class="pt-32 container-wide py-20 flex justify-center items-center min-h-[60vh]">
        <div class="max-w-md w-full rounded-3xl border border-border bg-card p-10 text-center shadow-sm">
            <span class="grid place-items-center h-14 w-14 rounded-full bg-primary/10 text-primary mx-auto">
                <Check class="h-6 w-6" />
            </span>
            <h1 class="mt-6 font-heading text-2xl">You've unsubscribed successfully</h1>
            <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                We have removed <strong class="text-foreground">{{ email }}</strong> from our newsletter subscription
                list. You will no longer receive automatic portfolio updates or stories.
            </p>
            <div class="mt-8 flex flex-wrap gap-3 justify-center">
                <Link href="/" class="btn-primary">Return Home
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </div>
</template>