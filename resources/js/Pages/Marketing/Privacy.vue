<script setup>
import { computed } from "vue"
import { Link, Head } from "@inertiajs/vue3"
import { ArrowRight, ShieldCheck } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

const HERO = "/assets/public/images/Privacy_policy_hero.jpeg"

const sections = [
    "Information we collect",
    "How we use it",
    "Sharing",
    "Cookies & tracking",
    "Data retention",
    "Your rights",
    "Security",
    "International transfers",
    "Children",
    "Changes",
    "Contact"
]

const blocks = computed(() => [
    {
        h: "1. Information we collect",
        p: [
            "Information you provide directly through forms on this site — such as your name, email, phone, organisation and RFP or agency-enquiry details.",
            "Information collected automatically, such as pages visited, device type and approximate location."
        ],
        list: [
            "Contact and identity details",
            "Programme requirements you submit",
            "Communications you send us",
            "Basic technical and usage data"
        ]
    },
    {
        h: "2. How we use your information",
        p: [
            "We use your information to respond to enquiries, coordinate proposals with represented members, deliver our services, and improve the website.",
            "We may use contact details to share relevant portfolio updates where you have opted in to receive them."
        ]
    },
    {
        h: "3. Sharing your information",
        p: [
            "We share enquiry details with the specific represented members relevant to your request, and with HMP Agency where you request agency support.",
            "We do not sell your personal information. We share data with service providers who process it on our behalf under appropriate safeguards."
        ]
    },
    {
        h: "4. Cookies and tracking",
        p: [
            "We use essential cookies to operate the site and optional analytics cookies to understand usage. See our Cookie Policy for details.",
            "You can manage cookie preferences in your browser."
        ]
    },
    {
        h: "5. Data retention",
        p: [
            "We keep personal information only as long as necessary for the purposes set out in this policy, and as required by law or legitimate business records."
        ]
    },
    {
        h: "6. Your rights",
        p: [
            "Depending on your location, you may request access to, correction of, or deletion of your personal data, and object to or restrict certain processing.",
            "To exercise these rights, contact us at hello@hmphospitality.co."
        ]
    },
    {
        h: "7. Security",
        p: [
            "We use appropriate technical and organisational measures to protect your information, but no method of transmission or storage is fully secure."
        ]
    },
    {
        h: "8. International transfers",
        p: [
            "Your information may be processed outside the country where it was collected. We take steps to ensure appropriate protection is in place for such transfers."
        ]
    },
    {
        h: "9. Children's privacy",
        p: [
            "This site is intended for travel industry professionals and business buyers. We do not knowingly collect personal information from children."
        ]
    },
    {
        h: "10. Changes to this policy",
        p: [
            "We may update this policy from time to time. The effective date above reflects the most recent revision."
        ]
    },
    {
        h: "11. Contact us",
        p: [
            "For any privacy questions, write to hello@hmphospitality.co or use our contact page."
        ]
    },
])

defineOptions({
    layout: Layout
})
</script>

<template>
    <!-- Dynamic World-Class SEO Metadata Block -->

    <Head>
        <title>Privacy Policy</title>
        <meta name="description"
            content="Read HMP Hospitality's Privacy Policy. Learn how we collect, process, protect, and respect your personal information." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Privacy Policy — HMP Hospitality" />
        <meta property="og:description"
            content="We respect your privacy. Read our terms on how we handle personal data, cookies, and international transfers safely." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80" />
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
                    <ShieldCheck class="h-4 w-4 text-primary" /> Legal
                </span>
                <h1 class="mt-3 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight">Privacy Policy</h1>
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

                <!-- Content Blocks -->
                <Reveal class="max-w-2xl space-y-10">
                    <p class="text-lg leading-relaxed text-muted-foreground">
                        HMP Hospitality ("we", "us") respects your privacy. This policy explains what personal
                        information we collect, how we use it, and the choices you have.
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
                                class="flex gap-3 text-[15px] text-muted-foreground align-top">
                                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                                {{ l }}
                            </li>
                        </ul>
                    </article>

                    <!-- Sidebar Helper contact box -->
                    <div class="rounded-2xl border border-border bg-card p-7">
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Questions about your privacy? Write to
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
