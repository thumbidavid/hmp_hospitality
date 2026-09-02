<script setup>
import { ref, reactive, computed } from "vue"
import { Head, Link, usePage, router } from "@inertiajs/vue3"
import { ArrowRight, Check, Upload, Compass, Building2, Users, Plane, CalendarClock, Megaphone, Sparkles, ListChecks } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"
import SectionHead from "@/Components/Public/shared/SectionHead.vue"

const AGENCY_HERO = "https://images.unsplash.com/photo-1571003123894-1f0594d4b0c5?auto=format&fit=crop&w=2000&q=80"
const stepLabels = ["Contact", "Programme", "Services", "Budget & needs", "Review"]

const orgTypes = ["Corporation", "Association", "NGO", "Government", "Travel Management Company", "Travel Agency", "Tour Operator", "Meeting & Events Agency", "Professional Conference Organiser", "Incentive Agency", "Luxury Travel Advisor", "Consortia", "Other"]
const programmeTypes = ["Business travel", "Corporate meeting", "Conference or congress", "Association event", "Government or NGO programme", "Incentive programme", "Product launch", "Awards or gala event", "Group travel", "Destination experience", "Other"]
const serviceOptions = ["Air travel", "Accommodation sourcing", "Venue sourcing", "Airport meet and assist", "Ground transportation", "Destination management", "Tours and excursions", "Dining experiences", "Meetings management", "Conference management", "Event production", "Audiovisual production", "Branding and signage", "Registration", "Delegate management", "Entertainment", "VIP and protocol services", "Onsite staffing", "Post-event reporting"]
const audiences = ["Corporations", "Associations", "NGOs", "Government institutions", "International organisations", "Meeting & events agencies", "Professional conference organisers", "Incentive planners", "Corporate travel buyers", "Incoming international groups"]

const serviceCategories = [
    { icon: Plane, title: "Business Travel", statement: "Coordinated travel solutions designed around your organisation, travellers and programme requirements.", items: ["Corporate air travel", "Group travel coordination", "Corporate accommodation", "Travel policy support", "Traveller profiles", "Itinerary management", "Travel documentation support", "Travel reporting", "Emergency traveller support"] },
    { icon: Compass, title: "Destination Management", statement: "Local knowledge and reliable coordination from arrival to departure.", items: ["Airport meet & assist", "Ground transportation", "Destination logistics", "Tours & excursions", "Dining experiences", "Cultural experiences", "VIP & protocol services", "Local supplier coordination", "Onsite programme support"] },
    { icon: Users, title: "Meetings & Events", statement: "Structured planning and delivery for meetings and events of every scale.", items: ["Venue & hotel sourcing", "Conference management", "Corporate meetings", "Association events", "Incentive programmes", "Product launches", "Awards & gala events", "Registration & attendee management", "Onsite management", "Post-event reporting"] },
    { icon: Megaphone, title: "Event Production", statement: "Creative and technical coordination that brings the event experience to life.", items: ["Creative concept development", "Event design", "Programme development", "Audiovisual coordination", "Staging", "Branding & signage", "Entertainment", "Event furniture & décor", "Show calling", "Onsite technical coordination"] },
]

const howItWorks = [
    { icon: ListChecks, title: "Share Your Brief", desc: "Tell us about your destination, travel, meeting or event requirements." },
    { icon: Compass, title: "Programme Development", desc: "We develop a coordinated programme and identify the required services and partners." },
    { icon: Check, title: "Proposal & Confirmation", desc: "We present a clear proposal, budget and delivery plan for your approval." },
    { icon: Sparkles, title: "Delivery & Reporting", desc: "We coordinate suppliers, manage onsite delivery and provide post-programme reporting." },
]

const page = usePage()

const step = ref(0)
const submitting = ref(false)
const services = ref([])

const f = reactive({
    fullName: "", jobTitle: "", organisation: "", orgType: "", email: "", telephone: "", country: "", commMethod: "Email",
    programmeName: "", programmeType: "", preferredCountry: "", preferredDestination: "", arrivalDate: "", departureDate: "", eventStart: "", eventEnd: "", numTravellers: "", numAttendees: "", numGuestrooms: "", numRoomNights: "", flexibleDates: false,
    budget: "", currency: "USD", accessibility: "", sustainability: "", vipRequirements: "", specialRequirements: "", proposalDeadline: "", decisionDate: "", additionalInformation: "",
    acceptPrivacy: false, shareWithSuppliers: false,
})

// Read success states from controller flash sessions
const done = computed(() => page.props.flash?.success || null)

const toggleService = (s) => {
    if (services.value.includes(s)) {
        services.value = services.value.filter((x) => x !== s)
    } else {
        services.value.push(s)
    }
}

const next = () => { step.value = Math.min(step.value + 1, 4) }
const back = () => { step.value = Math.max(step.value - 1, 0) }

const isValid = computed(() => {
    if (step.value === 0) return f.fullName && f.email && f.organisation
    if (step.value === 1) return true
    if (step.value === 2) return services.value.length > 0
    return true
})

const submit = () => {
    submitting.value = true

    // Package parameters inside structured payload
    const payload = {
        ...f,
        servicesRequired: services.value
    }

    router.post('/hmp-agency', payload, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false
        }
    })
}

defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>Complete Programme Support — HMP Agency</title>
        <meta name="description"
            content="HMP Agency provides complete business travel management, destination logistics, meetings, event production, and on-site event coordination across Africa." />
        <meta name="keywords"
            content="destination management company, event production, business travel Africa, DMC Nairobi, corporate meetings coordinator" />
        <meta property="og:title" content="HMP Agency — Beyond the Property. Complete Programme Support." />
        <meta property="og:description"
            content="Coordinate your corporate travel, destination management, event production, and meeting logistics under one specialist partner." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1571003123894-1f0594d4b0c5?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <!-- Post-submission Success Screen -->
    <div v-if="done" class="pt-32 container-wide py-20">
        <div class="max-w-2xl mx-auto rounded-3xl border border-border bg-card p-10 lg:p-14 text-center">
            <span class="grid place-items-center h-16 w-16 rounded-full bg-primary text-primary-foreground mx-auto">
                <Check class="h-8 w-8" />
            </span>
            <h1 class="mt-6 font-heading text-3xl sm:text-4xl">Your service request has been received.</h1>
            <p class="mt-4 text-muted-foreground">
                Thank you, {{ f.fullName.split(" ")[0] || "there" }}. The HMP Agency team will review your brief and
                respond within one business day.
            </p>
            <div
                class="mt-8 inline-flex flex-col items-center rounded-2xl border border-border bg-background px-8 py-5">
                <span class="text-xs uppercase tracking-wider text-muted-foreground">Your enquiry reference</span>
                <span class="mt-1 font-heading text-2xl text-primary">{{ done.ref }}</span>
            </div>
            <p v-if="done.note" class="mt-5 text-sm text-muted-foreground">
                {{ done.note }}
            </p>
            <div class="mt-9 flex flex-wrap gap-4 justify-center">
                <Link href="/" class="btn-primary">Return home</Link>
                <Link href="/portfolio" class="btn-ghost">Explore HMP Hospitality</Link>
            </div>
        </div>
    </div>

    <!-- Page Body -->
    <div v-else>
        <!-- Hero Section -->
        <section class="relative h-[60vh] min-h-[24rem] flex items-end overflow-hidden">
            <img :src="AGENCY_HERO" alt="HMP Agency — complete programme support"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/25" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">HMP Agency · Hotel and Meeting Planner
                    Ltd</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl lg:text-6xl leading-tight text-balance">Beyond the
                    Property. Complete Programme Support.</h1>
                <p class="mt-4 max-w-2xl text-white/85 leading-relaxed">HMP Agency provides business travel, destination
                    management, meetings and events services for organisations planning programmes in Kenya and across
                    Africa.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#service-request" class="btn-primary">Discuss Your Programme
                        <ArrowRight class="h-4 w-4" />
                    </a>
                    <a href="#services" class="btn-light">Explore Our Services</a>
                </div>
            </div>
        </section>

        <!-- Introduction -->
        <section class="container-wide py-16 lg:py-24">
            <Reveal class="max-w-3xl">
                <span class="eyebrow">One partner. Complete programme coordination.</span>
                <h2 class="mt-3 font-heading text-3xl sm:text-4xl leading-tight text-balance">From business travel and
                    destination logistics to meetings, events and onsite delivery, HMP Agency coordinates the services
                    surrounding your programme.</h2>
                <p class="mt-6 text-muted-foreground leading-relaxed">Our team works with corporate organisations,
                    associations, NGOs, government institutions, agencies and international groups to deliver
                    professionally managed travel and event experiences.</p>
            </Reveal>
        </section>

        <!-- Business unit relationship -->
        <section class="bg-sand/60">
            <div class="container-wide py-16 lg:py-24">
                <SectionHead eyebrow="Our business units" title="Two specialist teams, one connected platform" />
                <div class="grid gap-6 lg:grid-cols-2">
                    <Reveal>
                        <div class="h-full rounded-3xl border border-border bg-card p-8">
                            <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                                <Building2 class="h-5 w-5" />
                            </span>
                            <h3 class="mt-5 font-heading text-2xl">HMP Hospitality</h3>
                            <p class="mt-3 text-muted-foreground leading-relaxed">Find and source represented hotels,
                                resorts, venues, serviced residences and destinations.</p>
                        </div>
                    </Reveal>

                    <Reveal :delay="0.08">
                        <div class="h-full rounded-3xl border border-border bg-card p-8">
                            <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                                <Compass class="h-5 w-5" />
                            </span>
                            <h3 class="mt-5 font-heading text-2xl">HMP Agency</h3>
                            <p class="mt-3 text-muted-foreground leading-relaxed">Coordinate the travel, destination,
                                meeting and event services around the programme.</p>
                        </div>
                    </Reveal>
                </div>

                <Reveal class="mt-8">
                    <p class="text-sm text-muted-foreground leading-relaxed max-w-3xl">
                        <strong class="text-foreground">Disclosure:</strong> HMP Hospitality and HMP Agency are
                        specialist business units of Hotel and Meeting Planner Ltd. HMP Hospitality provides
                        representation and sourcing for hotels, venues, serviced residences and destinations, while HMP
                        Agency provides business travel, destination management, meetings and events services.
                    </p>
                </Reveal>
            </div>
        </section>

        <!-- Services -->
        <section id="services" class="container-wide py-16 lg:py-24">
            <SectionHead eyebrow="Our services" title="Four primary service categories"
                sub="Coordinated end to end — or scoped to exactly the services your programme needs." />
            <div class="grid gap-6 md:grid-cols-2">
                <Reveal v-for="(c, i) in serviceCategories" :key="c.title" :delay="(i % 2) * 0.06">
                    <div class="h-full rounded-3xl border border-border bg-card p-8">
                        <div class="flex items-center gap-3">
                            <span class="grid place-items-center h-11 w-11 rounded-full bg-primary/10 text-primary">
                                <component :is="c.icon" class="h-5 w-5" />
                            </span>
                            <h3 class="font-heading text-2xl">{{ c.title }}</h3>
                        </div>
                        <p class="mt-4 text-sm text-muted-foreground italic">{{ c.statement }}</p>
                        <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                            <li v-for="it in c.items" :key="it"
                                class="flex items-start gap-2 text-sm text-foreground/80">
                                <Check class="h-4 w-4 mt-0.5 text-primary shrink-0" /> {{ it }}
                            </li>
                        </ul>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- Who we support -->
        <section class="bg-sand/60">
            <div class="container-wide py-16 lg:py-24">
                <SectionHead eyebrow="Who we support" title="Built for organisations planning programmes in Africa" />
                <div class="flex flex-wrap gap-2.5">
                    <span v-for="a in audiences" :key="a" class="chip">
                        {{ a }}
                    </span>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="container-wide py-16 lg:py-24">
            <SectionHead eyebrow="How it works" title="Four steps from brief to delivered programme" />
            <div class="grid gap-6 lg:grid-cols-4">
                <Reveal v-for="(s, i) in howItWorks" :key="s.title" :delay="i * 0.06">
                    <div class="h-full rounded-3xl border border-border bg-card p-7">
                        <span class="grid place-items-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <component :is="s.icon" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 font-heading text-lg">{{ i + 1 }}. {{ s.title }}</h3>
                        <p class="mt-3 text-sm text-muted-foreground leading-relaxed">{{ s.desc }}</p>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- HMP Connection Promo -->
        <section class="bg-ink text-white">
            <div class="container-wide py-16 lg:py-24 grid gap-8 lg:grid-cols-2 items-center">
                <Reveal>
                    <span class="eyebrow !text-primary">Need a hotel or venue?</span>
                    <h2 class="mt-3 font-heading text-3xl sm:text-4xl text-balance">HMP Agency can source through the
                        HMP Hospitality portfolio.</h2>
                    <p class="mt-5 text-ink-fg/70 leading-relaxed">Recommendations are based on programme requirements,
                        location, capacity, budget and overall suitability — you're never required to use HMP
                        Hospitality properties.</p>
                </Reveal>
                <Reveal :delay="0.08" class="flex flex-wrap gap-3 lg:justify-end">
                    <Link href="/portfolio" class="btn-primary">Explore the HMP Hospitality Portfolio
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link href="/rfp" class="btn-light">Submit a Property RFP</Link>
                </Reveal>
            </div>
        </section>

        <!-- Service request form -->
        <section id="service-request" class="container-wide py-16 lg:py-24">
            <SectionHead eyebrow="Tell us about your programme" title="Submit a service request"
                sub="Share your requirements and our team will coordinate a proposal." />

            <!-- Stepper indicators -->
            <div class="mt-8 flex items-center gap-2 sm:gap-4 overflow-x-auto pb-2">
                <template v-for="(s, i) in stepLabels" :key="s">
                    <button @click="i < step && (step = i)" :class="[
                        'flex items-center gap-2.5 whitespace-nowrap cursor-pointer',
                        i <= step ? 'text-foreground' : 'text-muted-foreground'
                    ]">
                        <span :class="[
                            'grid place-items-center h-8 w-8 rounded-full text-sm font-medium transition-colors',
                            i === step ? 'bg-primary text-primary-foreground' : i < step ? 'bg-foreground text-background' : 'bg-muted text-muted-foreground'
                        ]">
                            <Check v-if="i < step" class="h-4 w-4" />
                            <span v-else>{{ i + 1 }}</span>
                        </span>
                        <span class="text-sm font-medium hidden sm:block">{{ s }}</span>
                    </button>
                    <span v-if="i < stepLabels.length - 1" :class="[
                        'flex-1 h-px min-w-4',
                        i < step ? 'bg-foreground' : 'bg-border'
                    ]" />
                </template>
            </div>

            <!-- Form Container -->
            <div class="mt-6 rounded-3xl border border-border bg-card p-7 lg:p-10">
                <!-- Step 0: Contact info -->
                <div v-if="step === 0" class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Full
                            name<span class="text-primary">*</span></label>
                        <input type="text" v-model="f.fullName" required class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Job
                            title</label>
                        <input type="text" v-model="f.jobTitle" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Organisation<span
                                class="text-primary">*</span></label>
                        <input type="text" v-model="f.organisation" required class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Organisation
                            type</label>
                        <select v-model="f.orgType" class="input-field cursor-pointer">
                            <option value="">Select type</option>
                            <option v-for="o in orgTypes" :key="o" :value="o">{{ o }}</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Email<span
                                class="text-primary">*</span></label>
                        <input type="email" v-model="f.email" required class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Telephone</label>
                        <input type="text" v-model="f.telephone" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Country</label>
                        <input type="text" v-model="f.country" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Preferred
                            communication method</label>
                        <select v-model="f.commMethod" class="input-field cursor-pointer">
                            <option v-for="m in ['Email', 'Telephone', 'Video Call', 'WhatsApp']" :key="m" :value="m">{{
                                m }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Step 1: Programme specs -->
                <div v-if="step === 1" class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Programme
                            or event name</label>
                        <input type="text" v-model="f.programmeName" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Programme
                            type</label>
                        <select v-model="f.programmeType" class="input-field cursor-pointer">
                            <option value="">Select type</option>
                            <option v-for="o in programmeTypes" :key="o" :value="o">{{ o }}</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Preferred
                            country</label>
                        <input type="text" v-model="f.preferredCountry" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Preferred
                            destination</label>
                        <input type="text" v-model="f.preferredDestination" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Arrival
                            date</label>
                        <input type="date" v-model="f.arrivalDate" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Departure
                            date</label>
                        <input type="date" v-model="f.departureDate" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Event
                            start date & time</label>
                        <input type="datetime-local" v-model="f.eventStart" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Event
                            end
                            date & time</label>
                        <input type="datetime-local" v-model="f.eventEnd" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of
                            travellers</label>
                        <input type="number" v-model="f.numTravellers" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of
                            attendees</label>
                        <input type="number" v-model="f.numAttendees" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of
                            guestrooms</label>
                        <input type="number" v-model="f.numGuestrooms" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of
                            room nights</label>
                        <input type="number" v-model="f.numRoomNights" class="input-field" />
                    </div>
                    <label class="flex items-center gap-2.5 text-sm sm:col-span-2 cursor-pointer">
                        <input type="checkbox" v-model="f.flexibleDates" class="h-4 w-4 accent-[hsl(var(--primary))]" />
                        Dates are flexible
                    </label>
                </div>

                <!-- Step 2: Services required -->
                <div v-if="step === 2">
                    <label
                        class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Services
                        required</label>
                    <p class="text-sm text-muted-foreground mb-3">Select all that apply.</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in serviceOptions" :key="s" type="button" @click="toggleService(s)"
                            :class="['chip cursor-pointer', services.includes(s) ? 'chip-active' : '']">
                            {{ s }}
                        </button>
                    </div>
                </div>

                <!-- Step 3: Budget & Details -->
                <div v-if="step === 3" class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Indicative
                            budget</label>
                        <input type="number" v-model="f.budget" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Currency</label>
                        <select v-model="f.currency" class="input-field cursor-pointer">
                            <option v-for="c in ['USD', 'KES', 'ZAR', 'EUR', 'GBP', 'NGN', 'MAD']" :key="c" :value="c">
                                {{ c }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Accessibility
                            requirements</label>
                        <input type="text" v-model="f.accessibility" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Sustainability
                            requirements</label>
                        <input type="text" v-model="f.sustainability" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">VIP
                            requirements</label>
                        <input type="text" v-model="f.vipRequirements" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Special
                            requirements</label>
                        <input type="text" v-model="f.specialRequirements" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Proposal
                            deadline</label>
                        <input type="date" v-model="f.proposalDeadline" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Decision
                            date</label>
                        <input type="date" v-model="f.decisionDate" class="input-field" />
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Additional
                            information</label>
                        <textarea v-model="f.additionalInformation" rows="3" class="input-field resize-none" />
                    </div>
                    <!-- File upload placeholder -->
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">File
                            upload</label>
                        <div
                            class="flex items-center justify-center rounded-xl border border-dashed border-border bg-background px-4 py-8 text-center cursor-pointer hover:bg-muted/10 transition-colors">
                            <div>
                                <Upload class="h-5 w-5 text-primary mx-auto" />
                                <p class="mt-2 text-sm text-muted-foreground">Drop a PDF or click to upload</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Summary & Confirmation -->
                <div v-if="step === 4" class="space-y-5">
                    <div class="rounded-2xl border border-border bg-background p-6 text-sm space-y-5">
                        <!-- Contact Review group -->
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2 font-semibold">Contact
                            </p>
                            <dl class="grid gap-1.5">
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Name</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.fullName }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Organisation</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.organisation }}</dd>
                                </div>
                                <div v-if="f.orgType" class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Type</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.orgType }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Email</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.email }}</dd>
                                </div>
                                <div v-if="f.telephone"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Telephone</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.telephone }}</dd>
                                </div>
                                <div v-if="f.country" class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Country</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.country }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Programme Review group -->
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2 font-semibold">
                                Programme</p>
                            <dl class="grid gap-1.5">
                                <div v-if="f.programmeName"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Programme</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.programmeName }}</dd>
                                </div>
                                <div v-if="f.programmeType"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Type</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.programmeType }}</dd>
                                </div>
                                <div v-if="f.preferredDestination || f.preferredCountry"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Destination</dt>
                                    <dd class="text-right font-medium text-foreground">
                                        {{ f.preferredDestination ? f.preferredDestination + ', ' : '' }}{{
                                            f.preferredCountry }}
                                    </dd>
                                </div>
                                <div v-if="f.arrivalDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Arrival</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.arrivalDate }}</dd>
                                </div>
                                <div v-if="f.departureDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Departure</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.departureDate }}</dd>
                                </div>
                                <div v-if="f.numTravellers"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Travellers</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.numTravellers }}</dd>
                                </div>
                                <div v-if="f.numAttendees"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Attendees</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.numAttendees }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Services Review group -->
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2 font-semibold">
                                Services</p>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="s in services" :key="s" class="chip chip-active">
                                    {{ s }}
                                </span>
                            </div>
                        </div>

                        <!-- Budget Review group -->
                        <div>
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2 font-semibold">Budget
                            </p>
                            <dl class="grid gap-1.5">
                                <div v-if="f.budget" class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Budget</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.budget }} {{ f.currency }}
                                    </dd>
                                </div>
                                <div v-if="f.proposalDeadline"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Proposal deadline</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.proposalDeadline }}</dd>
                                </div>
                                <div v-if="f.decisionDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Decision date</dt>
                                    <dd class="text-right font-medium text-foreground">{{ f.decisionDate }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Legal consents -->
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" v-model="f.acceptPrivacy"
                            class="h-4 w-4 mt-0.5 accent-[hsl(var(--primary))]" />
                        <span class="text-sm text-muted-foreground">I accept the Privacy Policy and give permission to
                            be
                            contacted.</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" v-model="f.shareWithSuppliers"
                            class="h-4 w-4 mt-0.5 accent-[hsl(var(--primary))]" />
                        <span class="text-sm text-muted-foreground">I consent to HMP Agency sharing relevant details
                            with contracted
                            suppliers to prepare or coordinate the services requested.</span>
                    </label>
                </div>

                <!-- Navigation Controls -->
                <div class="mt-6 flex items-center justify-between">
                    <button @click="back" :disabled="step === 0"
                        class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground disabled:opacity-40 cursor-pointer">
                        Back
                    </button>

                    <!-- Continue to next step -->
                    <button v-if="step < 4" @click="next" :disabled="!isValid"
                        class="btn-primary disabled:opacity-50 cursor-pointer">
                        Continue
                        <ArrowRight class="h-4 w-4" />
                    </button>

                    <!-- Submit Request -->
                    <button v-else @click="submit" :disabled="!f.acceptPrivacy || !f.shareWithSuppliers || submitting"
                        class="btn-primary disabled:opacity-50 cursor-pointer">
                        {{ submitting ? "Submitting…" : "Submit Service Request" }}
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>