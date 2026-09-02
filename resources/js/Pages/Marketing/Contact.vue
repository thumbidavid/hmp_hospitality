<script setup>
import { ref, reactive, computed } from "vue"
import { Head, Link, usePage, router } from "@inertiajs/vue3"
import { Mail, Phone, MapPin, Clock, Send, ArrowRight, MessageSquare } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

const details = [
    { icon: Mail, label: "Email", value: "hello@hmphospitality.co", href: "mailto:hello@hmphospitality.co" },
    { icon: Phone, label: "Telephone", value: "+254 703 720 000", href: "tel:+254703720000" },
    { icon: MapPin, label: "Office", value: "6th Floor, MJ1 Business Park, Westlands Road, Westlands, Nairobi, Kenya" },
    { icon: Clock, label: "Hours", value: "Monday–Friday · 8:30–17:30 EAT" },
]

const page = usePage()

const submitting = ref(false)

const form = reactive({
    name: "",
    email: "",
    subject: "",
    message: ""
})

// Read success states from controller flash session
const sent = computed(() => !!page.props.flash?.success)

const submit = () => {
    submitting.value = true
    router.post('/contact', form, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            submitting.value = false
            if (sent.value) {
                // Clear fields on success
                form.name = ""
                form.email = ""
                form.subject = ""
                form.message = ""
            }
        }
    })
}

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>Contact Us</title>
        <meta name="description"
            content="Get in touch with HMP Hospitality's office in Nairobi, Kenya. Ask us about sales representation, advisory services, or property sourcing." />
        <meta name="keywords"
            content="contact HMP Hospitality, Nairobi office, sales representation desk, travel advisor contacts" />
        <meta property="og:title" content="Contact Us — HMP Hospitality" />
        <meta property="og:description"
            content="Speak with our team about sourcing, market representation, advisory, or mutual partnerships." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1551882547-ff40ac63a5e1?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div class="pt-28">
        <!-- Introduction Header -->
        <section class="container-wide py-12 lg:py-20">
            <Reveal class="max-w-3xl">
                <span class="eyebrow">Contact HMP Hospitality</span>
                <h1 class="mt-3 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">
                    Let's talk about your programme, property, destination or partnership.
                </h1>
                <p class="mt-6 text-lg text-muted-foreground leading-relaxed">
                    Speak with the HMP Hospitality team about sourcing, representation or a partnership — or submit a
                    structured RFP for a faster, coordinated response.
                </p>
            </Reveal>
        </section>

        <!-- Contact Options Quick Cards -->
        <section class="container-wide grid gap-6 sm:grid-cols-2 lg:grid-cols-4 pb-4">
            <Reveal v-for="(d, i) in details" :key="d.label" :delay="i * 0.05">
                <a v-if="d.href" :href="d.href"
                    class="block h-full rounded-3xl border border-border bg-card p-7 transition-colors hover:border-primary/40">
                    <span class="grid place-items-center h-11 w-11 rounded-full bg-primary/10 text-primary">
                        <component :is="d.icon" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-5 text-xs font-medium uppercase tracking-[0.2em] text-muted-foreground">
                        {{ d.label }}
                    </h3>
                    <p class="mt-2 text-sm text-foreground leading-relaxed">
                        {{ d.value }}
                    </p>
                </a>
                <div v-else class="h-full rounded-3xl border border-border bg-card p-7">
                    <span class="grid place-items-center h-11 w-11 rounded-full bg-primary/10 text-primary">
                        <component :is="d.icon" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-5 text-xs font-medium uppercase tracking-[0.2em] text-muted-foreground">
                        {{ d.label }}
                    </h3>
                    <p class="mt-2 text-sm text-foreground leading-relaxed">
                        {{ d.value }}
                    </p>
                </div>
            </Reveal>
        </section>

        <!-- Dynamic Message Form and Address block -->
        <section class="container-wide grid gap-10 lg:grid-cols-2 py-16 lg:py-24">
            <Reveal>
                <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                    <MessageSquare class="h-5 w-5" />
                </span>

                <h2 class="mt-6 font-heading text-3xl">Send us a message</h2>
                <p class="mt-3 text-muted-foreground leading-relaxed">
                    For programme sourcing across multiple hotels, DMCs or agencies, the RFP form gives us the detail we
                    need to coordinate — but for a quick note, this works.
                </p>

                <!-- Onboarding success card -->
                <div v-if="sent" class="mt-8 rounded-3xl border border-primary/30 bg-primary/5 p-8 text-center">
                    <p class="font-heading text-2xl">Thank you — your message has been noted.</p>
                    <p class="mt-3 text-muted-foreground">A member of the HMP Hospitality team will be in touch shortly.
                    </p>
                    <button @click="router.reload()" class="btn-ghost mt-6 cursor-pointer">
                        Send another
                    </button>
                </div>

                <!-- Interactive form structure -->
                <form v-else @submit.prevent="submit" class="mt-8 grid gap-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="block text-xs font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Full
                                name</label>
                            <input required v-model="form.name" class="input-field" placeholder="Your name" />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Email</label>
                            <input required type="email" v-model="form.email" class="input-field"
                                placeholder="you@company.com" />
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Subject</label>
                        <input v-model="form.subject" class="input-field" placeholder="What's this about?" />
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Message</label>
                        <textarea required v-model="form.message" rows="5" class="input-field resize-none"
                            placeholder="Tell us a little about your programme or question…" />
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <button :disabled="submitting" class="btn-primary cursor-pointer disabled:opacity-50">
                            {{ submitting ? 'Sending...' : 'Send message' }}
                            <Send class="h-4 w-4" />
                        </button>
                        <Link href="/rfp" class="editorial-link">
                            Or submit an RFP
                            <ArrowRight class="h-4 w-4 arr" />
                        </Link>
                    </div>
                </form>
            </Reveal>

            <!-- Fixed Sidebar Location detail card -->
            <Reveal :delay="0.08">
                <div class="h-full rounded-3xl bg-ink text-ink-foreground p-8 lg:p-10">
                    <h3 class="font-heading text-2xl text-white">HMP Hospitality</h3>
                    <p class="mt-4 text-ink-foreground/70 leading-relaxed">
                        Hotel and Meeting Planner Ltd — bespoke sales & marketing representation for independent hotels,
                        resorts, lodges, venues, serviced residences and destinations.
                    </p>
                    <div class="mt-8 space-y-5">
                        <div class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-primary shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-white">Office</p>
                                <p class="text-sm text-ink-foreground/70 leading-relaxed">6th Floor, MJ1 Business
                                    Park,<br />Westlands Road, Westlands, Nairobi, Kenya</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Mail class="h-5 w-5 text-primary shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-white">Email</p>
                                <a href="mailto:hello@hmphospitality.co"
                                    class="text-sm text-ink-foreground/70 hover:text-white transition-colors">hello@hmphospitality.co</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Phone class="h-5 w-5 text-primary shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-white">Telephone</p>
                                <a href="tel:+254703720000"
                                    class="text-sm text-ink-foreground/70 hover:text-white transition-colors">+254 703
                                    720 000</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Clock class="h-5 w-5 text-primary shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-white">Hours</p>
                                <p class="text-sm text-ink-foreground/70 leading-relaxed">Monday–Friday · 8:30–17:30
                                    EAT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </Reveal>
        </section>
    </div>
</template>