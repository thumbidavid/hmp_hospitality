<script setup>
import { computed } from "vue"
import { Link, Head } from "@inertiajs/vue3"
import { ArrowRight, Cookie } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

const HERO = "/assets/public/images/Cookie_policy_hero.jpeg"

const sections = ["What cookies are", "Cookies we use", "Managing cookies", "Third parties", "Changes", "Contact"]
const header = { name: "Cookie", purpose: "Purpose", type: "Type" }

const blocks = computed(() => [
    {
        h: "1. What are cookies",
        p: [
            "Cookies are small text files stored on your device when you visit a website. They help the site function and understand usage.",
            "This site uses essential cookies and, where permitted, optional analytics cookies."
        ]
    },
    {
        h: "2. Cookies we use",
        p: ["The table below shows the main categories of cookies used on this site."],
        list: [
            { ...header },
            { name: "hmp_session", purpose: "Essential site function", type: "Essential" },
            { name: "hmp_prefs", purpose: "Remembers your shortlist & filters", type: "Essential" },
            { name: "hmp_analytics", purpose: "Anonymous usage statistics", type: "Analytics" },
            { name: "hmp_marketing", purpose: "Measure campaign effectiveness", type: "Optional" }
        ]
    },
    {
        h: "3. Managing cookies",
        p: ["You can control or delete cookies in your browser settings. Disabling essential cookies may affect site functionality."]
    },
    {
        h: "4. Third-party cookies",
        p: ["Some third-party tools we use (such as analytics providers) may set their own cookies. We encourage you to review their policies."]
    },
    {
        h: "5. Changes to this policy",
        p: ["We may update this Cookie Policy as our use of cookies changes. The date above reflects the latest revision."]
    },
    {
        h: "6. Contact us",
        p: ["For cookie-related questions, write to hello@hmphospitality.co."]
    }
])

defineOptions({
    layout: Layout
})
</script>

<template>
    <!-- Dynamic World-Class SEO Metadata Block -->

    <Head>
        <title>Cookie Policy</title>
        <meta name="description"
            content="Read HMP Hospitality's Cookie Policy. Learn how we use cookies, tracking technologies, and how to manage them on our site." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Cookie Policy — HMP Hospitality" />
        <meta property="og:description"
            content="Transparent cookie usage terms. Learn about essential site cookies, analytics, and managing preferences." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[44vh] min-h-[18rem] flex items-end overflow-hidden">
            <img :src="HERO" alt="" class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink/88 via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="flex items-center gap-2 text-xs uppercase tracking-[0.28em] text-white/70">
                    <Cookie class="h-4 w-4 text-primary" /> Legal
                </span>
                <h1 class="mt-3 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight">Cookie Policy</h1>
                <p class="mt-3 text-sm text-white/70">Last updated: 2 September 2026</p>
            </div>
        </section>

        <!-- Main Content -->
        <section class="container-wide py-20 lg:py-28">
            <div class="grid gap-12 lg:grid-cols-[1fr_2.4fr]">
                <!-- Sidebar Navigation links (Desktop only) -->
                <aside class="hidden lg:block">
                    <div class="sticky top-28 space-y-2 text-sm">
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-ink/50 mb-3">On this page</p>
                        <a v-for="(s, i) in sections" :key="s" :href="`#${i}`"
                            class="block text-ink/70 hover:text-primary transition-colors py-1 font-medium">
                            {{ s }}
                        </a>
                    </div>
                </aside>

                <!-- Content Column -->
                <Reveal class="max-w-2xl space-y-10">
                    <p class="text-lg leading-relaxed text-muted-foreground">
                        This policy explains how HMP Hospitality uses cookies and similar technologies on this website.
                    </p>

                    <article v-for="(b, i) in blocks" :key="i" :id="String(i)" class="scroll-mt-28">
                        <h2 class="font-heading text-2xl text-ink">{{ b.h }}</h2>
                        <p v-for="(pText, j) in b.p" :key="j"
                            class="mt-4 text-[15px] leading-relaxed text-muted-foreground">
                            {{ pText }}
                        </p>

                        <!-- Dynamic cookies matrix mapping list array if present -->
                        <div v-if="b.list" class="mt-5 overflow-hidden rounded-xl border border-border">
                            <div v-for="(r, j) in b.list" :key="j" :class="[
                                'grid grid-cols-3 gap-4 px-4 py-3 text-[13px] leading-snug',
                                j === 0 ? 'bg-sand/40 font-medium text-ink' : 'text-muted-foreground border-t border-border'
                            ]">
                                <span>{{ r.name }}</span>
                                <span>{{ r.purpose }}</span>
                                <span>{{ r.type }}</span>
                            </div>
                        </div>
                    </article>

                    <!-- Sidebar Helper privacy box -->
                    <div class="rounded-2xl border border-border bg-card p-7">
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Cookie questions? Email
                            <a href="mailto:hello@hmphospitality.co"
                                class="text-primary font-medium">hello@hmphospitality.co</a>.
                        </p>
                        <Link href="/privacy-policy" class="btn-ghost mt-5">
                            Read our Privacy Policy
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
            </div>
        </section>
    </div>
</template>
