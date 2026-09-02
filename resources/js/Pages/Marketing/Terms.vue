<script setup>
import { computed } from "vue"
import { Link, Head } from "@inertiajs/vue3"
import { ArrowRight, FileText } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

const HERO = "https://images.unsplash.com/photo-1554224155-67235b462309?auto=format&fit=crop&w=1800&q=80"

const sections = ["Acceptance", "Use of the site", "Intellectual property", "RFP & enquiries", "No guarantee", "Links", "Liability", "Indemnity", "Governing law", "Changes", "Contact"]

const blocks = computed(() => [
    { h: "1. Acceptance of terms", p: ["By accessing or using this website you agree to these Terms of Use. If you do not agree, please do not use the site."] },
    { h: "2. Use of the site", p: ["This site is provided for information about HMP Hospitality and our represented members, and to facilitate requests for proposals.", "You agree not to misuse the site or attempt to disrupt its operation."], list: ["Use the site lawfully", "Respect intellectual property rights", "Do not transmit unlawful or misleading content"] },
    { h: "3. Intellectual property", p: ["All content on this site, including text, images and design, is owned by or licensed to HMP Hospitality and our members. You may not reproduce it without permission.", "Member and property names belong to their respective owners."] },
    { h: "4. RFP and enquiries", p: ["Submitting a request for proposal or enquiry does not create a binding contract. HMP Hospitality coordinates responses and is not a party to any agreement between buyers and members unless expressly agreed."] },
    { h: "5. No guarantee of results", p: ["While we aim to provide accurate information, we do not guarantee that any member will respond or that any booking or programme will result from an enquiry."] },
    { h: "6. Links to third parties", p: ["This site may link to third-party websites. We are not responsible for their content or practices."] },
    { h: "7. Limitation of liability", p: ["To the extent permitted by law, HMP Hospitality is not liable for indirect or consequential losses arising from your use of the site."] },
    { h: "8. Indemnity", p: ["You agree to indemnify HMP Hospitality against claims arising from your misuse of the site or breach of these terms."] },
    { h: "9. Governing law", p: ["These terms are governed by the laws of Kenya. Any disputes will be resolved in the courts of Nairobi, Kenya."] },
    { h: "10. Changes to these terms", p: ["We may update these terms from time to time. The date above reflects the most recent revision."] },
    { h: "11. Contact", p: ["For questions about these terms, contact hello@hmphospitality.co."] }
])

defineOptions({
    layout: Layout
})
</script>

<template>
    <!-- Dynamic World-Class SEO Metadata Block -->

    <Head>
        <title>Terms of Use</title>
        <meta name="description"
            content="Read HMP Hospitality's Terms of Use. Review our general website usage parameters, intellectual property, and legal terms." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Terms of Use — HMP Hospitality" />
        <meta property="og:description"
            content="Website usage terms. Review guidance on user conduct, intellectual property rights, liability, and governing law." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1554224155-67235b462309?auto=format&fit=crop&w=1200&q=80" />
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
                    <FileText class="h-4 w-4 text-primary" /> Legal
                </span>
                <h1 class="mt-3 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight">Terms of Use</h1>
                <p class="mt-3 text-sm text-white/70">Last updated: 2 September 2026</p>
            </div>
        </section>

        <!-- Policy content section -->
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
                        These terms govern your use of the HMP Hospitality website. By using this site you agree to
                        them.
                    </p>

                    <article v-for="(b, i) in blocks" :key="i" :id="String(i)" class="scroll-mt-28">
                        <h2 class="font-heading text-2xl text-ink">{{ b.h }}</h2>
                        <p v-for="(pText, j) in b.p" :key="j"
                            class="mt-4 text-[15px] leading-relaxed text-muted-foreground">
                            {{ pText }}
                        </p>

                        <!-- List attributes if present inside block -->
                        <ul v-if="b.list" class="mt-4 space-y-2">
                            <li v-for="(l, j) in b.list" :key="j"
                                class="flex gap-3 text-[15px] text-muted-foreground align-top leading-snug">
                                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                                {{ l }}
                            </li>
                        </ul>
                    </article>

                    <!-- Contact Team Box -->
                    <div class="rounded-2xl border border-border bg-card p-7">
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Questions about these terms? Email
                            <a href="mailto:hello@hmphospitality.co"
                                class="text-primary font-medium">hello@hmphospitality.co</a>.
                        </p>
                        <Link href="/contact" class="btn-ghost mt-5">
                            Contact Our Team
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
            </div>
        </section>
    </div>
</template>