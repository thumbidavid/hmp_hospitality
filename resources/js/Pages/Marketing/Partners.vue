<script setup>
import { ref, computed, reactive } from "vue"
import { Head, usePage, router } from "@inertiajs/vue3"
import { ArrowRight, Check, Upload, ArrowUpRight } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import SectionHead from "@/Components/Public/shared/SectionHead.vue"
import HmpAgencyCta from "@/Components/Public/HmpAgencyCta.vue"

const PARTNERS_HERO = "/assets/public/images/Partners_hero.jpeg"

const page = usePage()

// Read dynamic grouped partners directly from Laravel page props
const partnerGroupsList = computed(() => {
    if (page.props.partnerGroups && page.props.partnerGroups.length > 0) {
        return page.props.partnerGroups
    }

    // Fail-safe static preview if DB table is currently empty
    return [
        {
            category: "Global GDS Partners",
            blurb: "Strategic GDS distribution networks ensuring active live inventory rates.",
            partners: [
                { id: 1, name: "Amadeus", logo: null, website: "#" },
                { id: 2, name: "Sabre", logo: null, website: "#" }
            ]
        }
    ]
})

const portfolioCategories = computed(() => {
    return page.props.categoriesList && page.props.categoriesList.length > 0
        ? page.props.categoriesList
        : ["Hotels, Resorts & Lodges", "Conference & Unique Venues", "Serviced Residences", "Destinations & DMOs"]
})

const countries = computed(() => {
    return page.props.countriesList && page.props.countriesList.length > 0
        ? page.props.countriesList
        : ["Kenya", "South Africa", "Tanzania", "Rwanda", "Uganda"]
})

const done = computed(() => !!page.props.flash?.success)
const submitting = ref(false)

const form = reactive({
    orgName: "", category: "", contact: "", jobTitle: "", email: "", phone: "", country: "", city: "", website: "",
    units: "", capacity: "", currentMarkets: "", requiredMarkets: "", services: "", additional: ""
})

const submit = () => {
    submitting.value = true
    router.post('/partners', form, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            submitting.value = false
            if (done.value) {
                Object.keys(form).forEach(key => form[key] = "")
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
        <title>Our Partners</title>
        <meta name="description"
            content="Explore HMP Hospitality's distribution networks, GDS configurations, destination management companies, and strategic tourism board partners." />
        <meta name="keywords"
            content="GDS connectivity, travel consortia, Skal international, MPI associations, DMC network Africa" />
        <meta property="og:title" content="Our Strategic Partners — HMP Hospitality" />
        <meta property="og:description"
            content="Strategic alignments with premium GDS platforms, travel consortia, and industry associations to expand represented member reach." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[52vh] min-h-[22rem] flex items-end overflow-hidden">
            <img :src="PARTNERS_HERO" alt="Partnerships that extend our reach"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/20" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">Partners</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">Partnerships
                    That Extend Our Reach</h1>
                <p class="mt-4 max-w-2xl text-white/85 leading-relaxed">Represented members, destination partners,
                    industry associations and strategic institutional partners working with HMP Hospitality.</p>
            </div>
        </section>

        <!-- Dynamic Partners Group List -->
        <section class="container-wide py-16 lg:py-24 space-y-14">
            <Reveal v-for="(g, i) in partnerGroupsList" :key="g.category" :delay="(i % 2) * 0.05">
                <div class="grid gap-6 lg:grid-cols-[1fr_2fr] items-start">
                    <!-- Dynamic Category Info Column -->
                    <div>
                        <span class="eyebrow">{{ `0${i + 1}` }}</span>
                        <h2 class="mt-3 font-heading text-3xl leading-snug">{{ g.category }}</h2>
                        <p class="mt-4 text-muted-foreground leading-relaxed">{{ g.blurb }}</p>
                    </div>

                    <!-- Dynamic Partners Cards Grid -->
                    <ul class="grid gap-3 sm:grid-cols-2">
                        <li v-for="p in g.partners" :key="p.id"
                            class="flex items-center gap-4 rounded-2xl border border-border bg-card px-5 py-4 transition-colors hover:border-primary/40">
                            <!-- Circle image holder or initials fallback -->
                            <div
                                class="h-10 w-10 shrink-0 rounded-full bg-primary/10 text-primary text-sm font-semibold overflow-hidden flex items-center justify-center">
                                <img v-if="p.logo" :src="p.logo" :alt="p.name" class="h-full w-full object-cover" />
                                <span v-else>{{ p.name.slice(0, 1) }}</span>
                            </div>

                            <div class="flex flex-col">
                                <!-- Dynamic links to official website -->
                                <a v-if="p.website && p.website !== '#'" :href="p.website" target="_blank"
                                    rel="noreferrer"
                                    class="text-sm font-medium text-foreground hover:text-primary transition-colors flex items-center gap-1">
                                    {{ p.name }}
                                    <ArrowUpRight class="h-3 w-3" />
                                </a>
                                <span v-else class="text-sm font-medium text-foreground leading-snug">{{ p.name
                                }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </Reveal>
        </section>

        <!-- Onboarding Form -->
        <section class="bg-sand/60">
            <div class="container-wide py-20 lg:py-28">
                <SectionHead eyebrow="Become a represented member"
                    title="Take your property or destination to the right markets"
                    sub="We represent independent hotels, resorts, lodges, venues, serviced residences and destinations. Tell us about your business and our team will be in touch." />

                <div v-if="done"
                    class="rounded-3xl border border-border bg-card p-10 text-center max-w-3xl mx-auto shadow-sm">
                    <span
                        class="grid place-items-center h-14 w-14 rounded-full bg-primary text-primary-foreground mx-auto">
                        <Check class="h-6 w-6" />
                    </span>
                    <h3 class="mt-5 font-heading text-2xl">Thank you — your enquiry is in.</h3>
                    <p class="mt-3 text-sm text-muted-foreground">A member of the HMP Hospitality team will be in touch
                        within two business days.</p>
                    <button @click="router.reload()" class="btn-ghost mt-6 cursor-pointer">Submit another
                        enquiry</button>
                </div>

                <form v-else @submit.prevent="submit"
                    class="grid gap-5 sm:grid-cols-2 rounded-3xl border border-border bg-card p-7 lg:p-10 shadow-sm">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Organisation
                            or property name<span class="text-primary">*</span></label>
                        <input type="text" v-model="form.orgName" required class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Portfolio
                            category</label>
                        <select v-model="form.category" required class="input-field cursor-pointer">
                            <option value="">Select category</option>
                            <option v-for="c in portfolioCategories" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Contact
                            person<span class="text-primary">*</span></label>
                        <input type="text" v-model="form.contact" required class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Job
                            title</label>
                        <input type="text" v-model="form.jobTitle" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Email<span
                                class="text-primary">*</span></label>
                        <input type="email" v-model="form.email" required class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Telephone
                            number</label>
                        <input type="text" v-model="form.phone" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Country</label>
                        <select v-model="form.country" class="input-field cursor-pointer">
                            <option value="">Select country</option>
                            <option v-for="c in countries" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">City
                            or destination</label>
                        <input type="text" v-model="form.city" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Official
                            website</label>
                        <input type="text" v-model="form.website" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of guestrooms / units</label>
                        <input type="number" v-model="form.units" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Largest
                            meeting capacity (pax)</label>
                        <input type="number" v-model="form.capacity" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Current
                            source markets</label>
                        <input type="text" v-model="form.currentMarkets" class="input-field" />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Markets
                            requiring representation</label>
                        <input type="text" v-model="form.requiredMarkets" class="input-field" />
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Representation
                            services required</label>
                        <textarea v-model="form.services" rows="3" class="input-field resize-none"
                            placeholder="e.g. Strategic sales representation, account development, trade show representation, marketing support…" />
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Company
                            profile upload</label>
                        <div
                            class="flex items-center justify-center rounded-xl border border-dashed border-border bg-background px-4 py-8 text-center cursor-pointer hover:bg-muted/10 transition-colors">
                            <div>
                                <Upload class="h-5 w-5 text-primary mx-auto" />
                                <p class="mt-2 text-sm text-muted-foreground">Drop a PDF or click to upload</p>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Additional
                            information</label>
                        <textarea v-model="form.additional" rows="3" class="input-field resize-none"
                            placeholder="Anything else we should know…" />
                    </div>

                    <div class="sm:col-span-2 flex flex-wrap gap-4 items-center mt-3">
                        <button :disabled="submitting" class="btn-primary cursor-pointer disabled:opacity-50">
                            {{ submitting ? 'Sending...' : 'Send Enquiry' }}
                            <ArrowRight class="h-4 w-4" />
                        </button>
                        <p class="text-xs text-muted-foreground">By submitting you agree to our Privacy Policy.</p>
                    </div>
                </form>
            </div>
        </section>

        <!-- Dynamic HMP Agency cross cta banner -->
        <HmpAgencyCta />
    </div>
</template>
