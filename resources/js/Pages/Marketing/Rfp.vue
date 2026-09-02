<script setup>
import { ref, reactive, computed, onMounted } from "vue"
import { Head, usePage, router, Link } from "@inertiajs/vue3"
import { ArrowRight, ArrowLeft, Check, X, Heart, Upload } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import { useShortlistStore } from "@/Stores/shortlistStore"

const steps = ["Buyer details", "Requirement type", "Programme details", "Review & submit"]
const buyerTypes = ["Travel Advisor", "Tour Operator", "Travel Management Company", "Corporate Travel Buyer", "Meeting & Event Planner", "Association", "NGO", "Government", "Conference Organiser", "International Agency / Consortia", "Other"]
const requirementTypes = ["Business Travel", "Group Accommodation", "Conference or Meeting", "Incentive Programme", "Association Event", "Government or NGO Programme", "Leisure Group", "Long-stay Accommodation", "Venue-only Event", "Destination Enquiry"]
const currencies = ["USD", "KES", "ZAR", "EUR", "GBP", "NGN", "MAD"]
const agencyServices = ["Air travel and business travel", "Airport coordination", "Ground transportation", "Destination management", "Meetings and event management", "Delegate registration and logistics", "Event production", "Tours and experiences", "VIP and protocol services", "No additional support required"]
const RFP_HERO = "https://images.unsplash.com/photo-1542317638-a31dcf3aa345?auto=format&fit=crop&w=2000&q=80"

const page = usePage()
const shortlistStore = useShortlistStore()

const step = ref(0)
const agency = ref([])
const submitting = ref(false)
const showSuccessModal = ref(false) // Controls modal visibility

const f = reactive({
    plannerName: "", plannerTitle: "", plannerCompany: "", plannerEmail: "", plannerPhone: "", plannerCountry: "", buyerType: "", commMethod: "Email",
    programmeName: "", programmeType: "", preferredDestination: "", flexibleDestination: false, arrivalDate: "", departureDate: "", flexibleDates: false,
    numAttendees: "", numGuestrooms: "", numRoomNights: "", meetingSpaceReq: "", venueCapacity: "", fbRequirements: "", transferRequirements: "", additionalServices: "",
    budget: "", currency: "USD", specialRequirements: "", accessibility: "", sustainability: "", decisionDate: "", proposalDeadline: "",
    acceptPrivacy: false, agencyConsent: false,
})

onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    const pre = params.get("prop")

    if (pre) {
        const found = page.props.properties.find(p => p.slug === pre)
        if (found && !shortlistStore.has(pre)) {
            shortlistStore.toggle({ id: pre })
        }
    }
})

const chosenProps = computed(() => {
    return page.props.properties.filter(p => shortlistStore.has(p.slug))
})

const toggleProp = (slug) => {
    shortlistStore.toggle({ id: slug })
}

const toggleAgency = (s) => {
    if (s === "No additional support required") {
        agency.value = ["No additional support required"]
    } else {
        if (agency.value.includes(s)) {
            agency.value = agency.value.filter((x) => x !== s).filter((x) => x !== "No additional support required")
        } else {
            agency.value = agency.value.filter((x) => x !== "No additional support required")
            agency.value.push(s)
        }
    }
}

const next = () => { step.value = Math.min(step.value + 1, 3) }
const back = () => { step.value = Math.max(step.value - 1, 0) }

const isValid = computed(() => {
    if (step.value === 0) return f.plannerName && f.plannerEmail && f.buyerType
    if (step.value === 1) return f.requirementType
    return true
})

const wantsAgency = computed(() => agency.value.length > 0 && !agency.value.includes("No additional support required"))

// Retrieve flashed session details dynamically from page props
const successRef = computed(() => page.props.flash?.success_ref || "RFP-Enquiry")
const successName = computed(() => page.props.flash?.success_name || "Buyer")

const submit = () => {
    submitting.value = true

    const propertyIds = chosenProps.value.map(p => p.id)
    const mappedAgencyServices = agency.value.map(serviceName => {
        const found = page.props.agencyServices.find(s => s.name === serviceName)
        return found ? found.id : null
    }).filter(Boolean)

    const matchedCountry = page.props.countries.find(c => c.name === f.plannerCountry)
    const buyerCountryId = matchedCountry ? matchedCountry.id : null

    const matchedBuyerType = page.props.buyerTypes.find(b => b.name === f.buyerType)
    const buyerTypeId = matchedBuyerType ? matchedBuyerType.id : null

    const requirementTypeMap = {
        "Business Travel": "business_travel",
        "Group Accommodation": "group_accommodation",
        "Conference or Meeting": "conference_or_meeting",
        "Incentive Programme": "incentive_programme",
        "Association Event": "association_event",
        "Government or NGO Programme": "government_ngo_programme",
        "Leisure Group": "leisure_group",
        "Long-stay Accommodation": "long_stay_accommodation",
        "Venue-only Event": "venue_only_event",
        "Destination Enquiry": "destination_enquiry"
    }
    const requirementType = requirementTypeMap[f.requirementType] || "business_travel"

    let additionalRequirements = f.additionalServices
    if (propertyIds.length === 0) {
        additionalRequirements = `[SYSTEM ALERT: Buyer requested recommendations. No specific properties selected.]\n\n` + (f.additionalServices || '')
    }

    const payload = {
        full_name: f.plannerName,
        job_title: f.plannerTitle,
        company_name: f.plannerCompany,
        email: f.plannerEmail,
        phone: f.plannerPhone,
        buyer_country_id: buyerCountryId,
        buyer_type_id: buyerTypeId,
        preferred_communication_method: f.commMethod.toLowerCase().replace(' ', '_'),
        requirement_type: requirementType,
        programme_name: f.programmeName,
        preferred_destination: f.preferredDestination,
        is_destination_flexible: f.flexibleDestination,
        arrival_date: f.arrivalDate || null,
        departure_date: f.departureDate || null,
        is_dates_flexible: f.flexibleDates,
        number_of_attendees: f.numAttendees || null,
        number_of_rooms: f.numGuestrooms || null,
        number_of_room_nights: f.numRoomNights || null,
        meeting_room_requirements: f.meetingSpaceReq,
        venue_capacity: f.venueCapacity || null,
        fnb_requirements: f.fbRequirements,
        transfer_airport_requirements: f.transferRequirements,
        budget_amount: f.budget || null,
        currency: f.currency,
        accessibility_requirements: f.accessibility,
        sustainability_requirements: f.sustainability,
        additional_requirements: additionalRequirements,
        proposal_deadline: f.proposalDeadline || null,
        decision_date: f.decisionDate || null,

        properties: propertyIds,
        agency_services: mappedAgencyServices,
        privacy_policy_accepted: f.acceptPrivacy
    }

    router.post('/rfp', payload, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // 1. Reset all form inputs to default values
            Object.keys(f).forEach(key => {
                if (typeof f[key] === 'boolean') {
                    f[key] = false
                } else if (key === 'currency') {
                    f[key] = 'USD'
                } else if (key === 'commMethod') {
                    f[key] = 'Email'
                } else {
                    f[key] = ''
                }
            })
            agency.value = []
            step.value = 0

            // 2. Clear local storage shortlist
            shortlistStore.clear()

            // 3. Open the success overlay modal
            showSuccessModal.value = true
        },
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
        <title>Submit a Request for Proposal (RFP)</title>
        <meta name="description"
            content="Submit your brief to HMP Hospitality. Share your corporate meeting, group travel, incentive, or business events specs for coordinated proposals." />
        <meta name="keywords"
            content="hotel RFP Africa, corporate event proposal, conference RFP, venue sourcing request" />
        <meta property="og:title" content="Submit an RFP — HMP Hospitality" />
        <meta property="og:description"
            content="One brief, a curated shortlist, and coordinated like-for-like property and destination proposals." />
        <meta property="og:image"
            content="https://images.unsplash.com/photo-1542317638-a31dcf3aa345?auto=format&fit=crop&w=1200&q=80" />
        <meta property="og:type" content="website" />
        <meta name="robots" content="noindex, follow" /> <!-- Protects your submission funnel from index clutter -->
    </Head>

    <div>
        <!-- Hero Section -->
        <section class="relative h-[42vh] min-h-[20rem] flex items-end overflow-hidden">
            <img :src="RFP_HERO" alt="Submit an RFP"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-ink/25" />
            <div class="container-wide relative pt-28 pb-10 text-white">
                <span class="text-xs uppercase tracking-[0.28em] text-white/70">Submit an RFP</span>
                <h1 class="mt-2 font-heading text-4xl sm:text-5xl leading-tight text-balance">Tell Us What You're
                    Planning</h1>
                <p class="mt-3 max-w-xl text-white/85 leading-relaxed">Share your requirements and we will connect you
                    with the right properties and destinations.</p>
            </div>
        </section>

        <!-- Top Shortlist Banner Selector -->
        <section v-if="chosenProps.length > 0" class="container-wide pt-6">
            <div class="rounded-2xl border border-border bg-card p-4 flex flex-wrap items-center gap-3">
                <span class="text-xs uppercase tracking-wider text-muted-foreground mr-1">Shortlist</span>
                <span v-for="p in chosenProps" :key="p.id"
                    class="inline-flex items-center gap-2 rounded-full bg-background border border-border pl-3 pr-1 py-1 text-sm shadow-sm">
                    <Heart class="h-3.5 w-3.5 text-primary fill-current" />
                    {{ p.name }}
                    <button @click="toggleProp(p.slug)"
                        class="grid place-items-center h-5 w-5 rounded-full hover:bg-primary/10 cursor-pointer">
                        <X class="h-3 w-3" />
                    </button>
                </span>
            </div>
        </section>

        <!-- Stepper Indicators -->
        <section class="container-wide pt-6">
            <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto pb-2">
                <template v-for="(s, i) in steps" :key="s">
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
                    <span v-if="i < steps.length - 1" :class="[
                        'flex-1 h-px min-w-4',
                        i < step ? 'bg-foreground' : 'bg-border'
                    ]" />
                </template>
            </div>
        </section>

        <!-- Main Form & Sidebar Grid -->
        <section class="container-wide grid gap-10 lg:grid-cols-[1.6fr_1fr] py-10">
            <div>
                <!-- Step 0: Buyer details -->
                <div v-if="step === 0" class="grid gap-5 sm:grid-cols-2 rounded-3xl border border-border bg-card p-7">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Full
                            name<span class="text-primary">*</span></label>
                        <input type="text" v-model="f.plannerName" required class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Job
                            title</label>
                        <input type="text" v-model="f.plannerTitle" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Company
                            or organisation</label>
                        <input type="text" v-model="f.plannerCompany" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Email<span
                                class="text-primary">*</span></label>
                        <input type="email" v-model="f.plannerEmail" required class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Telephone</label>
                        <input type="text" v-model="f.plannerPhone" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Country</label>
                        <select v-model="f.plannerCountry" class="input-field cursor-pointer">
                            <option value="">Select country</option>
                            <option v-for="c in page.props.countries" :key="c.id" :value="c.name">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Buyer
                            type<span class="text-primary">*</span></label>
                        <select v-model="f.buyerType" class="input-field cursor-pointer">
                            <option value="">Select buyer type</option>
                            <option v-for="b in buyerTypes" :key="b" :value="b">{{ b }}</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Preferred
                            communication method</label>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="m in ['Email', 'Telephone', 'Video Call', 'WhatsApp']" :key="m" type="button"
                                @click="f.commMethod = m"
                                :class="['chip cursor-pointer', f.commMethod === m ? 'chip-active' : '']">
                                {{ m }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Requirement selection -->
                <div v-if="step === 1" class="rounded-3xl border border-border bg-card p-7">
                    <label
                        class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">What
                        are
                        you planning?</label>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <button v-for="r in requirementTypes" :key="r" type="button" @click="f.requirementType = r"
                            :class="[
                                'text-left rounded-2xl border p-4 text-sm transition-all cursor-pointer',
                                f.requirementType === r ? 'border-primary bg-primary/5' : 'border-border hover:border-foreground/30'
                            ]">
                            {{ r }}
                        </button>
                    </div>
                </div>

                <!-- Step 2: Programme details specs -->
                <div v-if="step === 2" class="grid gap-5 sm:grid-cols-2 rounded-3xl border border-border bg-card p-7">
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Programme
                            name</label>
                        <input type="text" v-model="f.programmeName" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Preferred
                            destination</label>
                        <input type="text" v-model="f.preferredDestination" class="input-field" />
                    </div>
                    <label class="flex items-end gap-2.5 text-sm pb-3 cursor-pointer">
                        <input type="checkbox" v-model="f.flexibleDestination"
                            class="h-4 w-4 accent-[hsl(var(--primary))]" />
                        Flexible on destination
                    </label>
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Selected
                            properties or venues</label>
                        <div v-if="chosenProps.length" class="flex flex-wrap gap-2">
                            <span v-for="p in chosenProps" :key="p.id"
                                class="inline-flex items-center gap-2 rounded-full border border-border px-3 py-1 text-sm shadow-sm bg-background">
                                <Heart class="h-3.5 w-3.5 text-primary fill-current" /> {{ p.name }}
                            </span>
                        </div>
                        <p v-else class="text-sm text-muted-foreground leading-relaxed">
                            None selected.
                            <Link href="/portfolio" class="text-primary hover:underline">Browse the portfolio</Link> to
                            add to
                            your shortlist.
                        </p>
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
                    <label class="flex items-center gap-2.5 text-sm sm:col-span-2 cursor-pointer">
                        <input type="checkbox" v-model="f.flexibleDates" class="h-4 w-4 accent-[hsl(var(--primary))]" />
                        Dates are flexible
                    </label>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of attendees</label>
                        <input type="number" v-model="f.numAttendees" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of guestrooms / units</label>
                        <input type="number" v-model="f.numGuestrooms" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Number
                            of room nights</label>
                        <input type="number" v-model="f.numRoomNights" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Meeting
                            room requirements</label>
                        <input type="text" v-model="f.meetingSpaceReq" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Venue
                            capacity</label>
                        <input type="number" v-model="f.venueCapacity" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Food
                            & beverage requirements</label>
                        <input type="text" v-model="f.fbRequirements" class="input-field" />
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Transfer
                            / airport requirements</label>
                        <input type="text" v-model="f.transferRequirements" class="input-field" />
                    </div>
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
                            <option v-for="c in currencies" :key="c" :value="c">{{ c }}</option>
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
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Additional
                            requirements</label>
                        <input type="text" v-model="f.additionalServices" class="input-field" />
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
                    <!-- File upload placeholder -->
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Upload
                            brief or supporting document</label>
                        <div
                            class="flex items-center justify-center rounded-xl border border-dashed border-border bg-background px-4 py-8 text-center cursor-pointer hover:bg-muted/10 transition-colors">
                            <div>
                                <Upload class="h-5 w-5 text-primary mx-auto" />
                                <p class="mt-2 text-sm text-muted-foreground">Drop a PDF or click to upload</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Review summary details -->
                <div v-if="step === 3" class="space-y-6">
                    <div class="rounded-3xl border border-border bg-card p-7 space-y-6">
                        <h3 class="font-heading text-2xl mb-6">Review your enquiry</h3>

                        <!-- Review: Planner profile info -->
                        <div class="text-sm">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2">Buyer</p>
                            <dl class="grid gap-1.5">
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Name</dt>
                                    <dd class="text-right font-medium">{{ f.plannerName }}</dd>
                                </div>
                                <div v-if="f.plannerTitle"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Title</dt>
                                    <dd class="text-right font-medium">{{ f.plannerTitle }}</dd>
                                </div>
                                <div v-if="f.plannerCompany"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Company</dt>
                                    <dd class="text-right font-medium">{{ f.plannerCompany }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Email</dt>
                                    <dd class="text-right font-medium">{{ f.plannerEmail }}</dd>
                                </div>
                                <div v-if="f.plannerPhone"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Phone</dt>
                                    <dd class="text-right font-medium">{{ f.plannerPhone }}</dd>
                                </div>
                                <div v-if="f.plannerCountry"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Country</dt>
                                    <dd class="text-right font-medium">{{ f.plannerCountry }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Buyer type</dt>
                                    <dd class="text-right font-medium">{{ f.buyerType }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Preferred contact</dt>
                                    <dd class="text-right font-medium">{{ f.commMethod }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Review: Programme specs -->
                        <div class="text-sm">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2">Requirement</p>
                            <dl class="grid gap-1.5">
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Requirement type</dt>
                                    <dd class="text-right font-medium">{{ f.requirementType }}</dd>
                                </div>
                                <div v-if="f.programmeName"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Programme</dt>
                                    <dd class="text-right font-medium">{{ f.programmeName }}</dd>
                                </div>
                                <div v-if="f.preferredDestination"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Destination</dt>
                                    <dd class="text-right font-medium">{{ f.preferredDestination }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Flexible destination</dt>
                                    <dd class="text-right font-medium">{{ f.flexibleDestination ? "Yes" : "—" }}</dd>
                                </div>
                                <div v-if="f.arrivalDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Arrival</dt>
                                    <dd class="text-right font-medium">{{ f.arrivalDate }}</dd>
                                </div>
                                <div v-if="f.departureDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Departure</dt>
                                    <dd class="text-right font-medium">{{ f.departureDate }}</dd>
                                </div>
                                <div class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Flexible dates</dt>
                                    <dd class="text-right font-medium">{{ f.flexibleDates ? "Yes" : "—" }}</dd>
                                </div>
                                <div v-if="f.numAttendees"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Attendees</dt>
                                    <dd class="text-right font-medium">{{ f.numAttendees }}</dd>
                                </div>
                                <div v-if="f.numGuestrooms"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Rooms / units</dt>
                                    <dd class="text-right font-medium">{{ f.numGuestrooms }}</dd>
                                </div>
                                <div v-if="f.numRoomNights"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Room nights</dt>
                                    <dd class="text-right font-medium">{{ f.numRoomNights }}</dd>
                                </div>
                                <div v-if="f.venueCapacity"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Venue capacity</dt>
                                    <dd class="text-right font-medium">{{ f.venueCapacity }}</dd>
                                </div>
                                <div v-if="f.fbRequirements"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">F&B</dt>
                                    <dd class="text-right font-medium">{{ f.fbRequirements }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Review: Budget & Requirements -->
                        <div class="text-sm">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2">Budget & requirements
                            </p>
                            <dl class="grid gap-1.5">
                                <div v-if="f.budget" class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Budget</dt>
                                    <dd class="text-right font-medium">{{ f.budget }} {{ f.currency }}</dd>
                                </div>
                                <div v-if="f.proposalDeadline"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Proposal deadline</dt>
                                    <dd class="text-right font-medium">{{ f.proposalDeadline }}</dd>
                                </div>
                                <div v-if="f.decisionDate"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Decision date</dt>
                                    <dd class="text-right font-medium">{{ f.decisionDate }}</dd>
                                </div>
                                <div v-if="f.accessibility"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Accessibility</dt>
                                    <dd class="text-right font-medium">{{ f.accessibility }}</dd>
                                </div>
                                <div v-if="f.sustainability"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Sustainability</dt>
                                    <dd class="text-right font-medium">{{ f.sustainability }}</dd>
                                </div>
                                <div v-if="f.additionalServices"
                                    class="flex justify-between gap-6 border-b border-border pb-1.5">
                                    <dt class="text-muted-foreground">Additional req.</dt>
                                    <dd class="text-right font-medium">{{ f.additionalServices }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Review: Chosen shortlist members -->
                        <div v-if="chosenProps.length > 0" class="text-sm">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground mb-2">Selected members</p>
                            <ul class="space-y-1.5">
                                <li v-for="p in chosenProps" :key="p.id" class="flex items-center gap-2">
                                    <Heart class="h-3.5 w-3.5 text-primary fill-current" /> {{ p.name }} — {{ p.city }},
                                    {{
                                        p.country }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Optional HMP Agency referral -->
                    <div class="rounded-3xl border border-border bg-sand/40 p-7">
                        <h3 class="font-heading text-xl">Do you require additional business travel, destination
                            management or
                            event-delivery support?</h3>
                        <p class="mt-2 text-sm text-muted-foreground">Our sister business unit, HMP Agency, can help —
                            select
                            any services you'd like to hear more about.</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button v-for="s in agencyServices" :key="s" type="button" @click="toggleAgency(s)"
                                :class="['chip cursor-pointer', agency.includes(s) ? 'chip-active' : '']">
                                {{ s }}
                            </button>
                        </div>
                        <div v-if="wantsAgency" class="mt-5 space-y-3">
                            <p class="text-xs text-muted-foreground leading-relaxed">
                                HMP Hospitality and HMP Agency are specialist business units of Hotel and Meeting
                                Planner Ltd.
                                HMP Hospitality provides representation and sourcing for hotels, venues, serviced
                                residences and
                                destinations, while HMP Agency provides business travel, destination management,
                                meetings and
                                events services.
                            </p>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" v-model="f.agencyConsent"
                                    class="h-4 w-4 mt-0.5 accent-[hsl(var(--primary))]" />
                                <span class="text-sm text-muted-foreground">I authorise HMP Hospitality to share the
                                    relevant
                                    details of this enquiry with HMP Agency for the purpose of preparing or coordinating
                                    the
                                    additional services requested.</span>
                            </label>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" v-model="f.acceptPrivacy"
                            class="h-4 w-4 mt-0.5 accent-[hsl(var(--primary))]" />
                        <span class="text-sm text-muted-foreground">I accept the Privacy Policy and consent to HMP
                            Hospitality
                            processing this enquiry and sharing it with selected represented members.</span>
                    </label>
                </div>
            </div>

            <!-- Sidebar list preview -->
            <aside class="lg:sticky lg:top-28 self-start space-y-5">
                <div class="rounded-3xl border border-border bg-card p-6 shadow-sm">
                    <h3 class="font-heading text-xl">Members in this RFP</h3>

                    <div v-if="chosenProps.length === 0" class="mt-3 text-sm text-muted-foreground space-y-3">
                        <p>No members selected yet.
                            <Link href="/portfolio" class="text-primary hover:underline">Browse the portfolio</Link> and
                            add
                            properties to your RFP.
                        </p>
                    </div>

                    <ul v-else class="mt-4 space-y-3">
                        <li v-for="p in chosenProps" :key="p.id" class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img :src="p.image" alt="" class="h-12 w-16 object-cover rounded-lg" />
                                <span>
                                    <span class="block text-sm font-medium text-foreground leading-snug">{{ p.name
                                        }}</span>
                                    <span class="block text-xs text-muted-foreground leading-snug">{{ p.city }}, {{
                                        p.country
                                        }}</span>
                                </span>
                            </div>
                            <button @click="toggleProp(p.slug)"
                                class="grid place-items-center h-7 w-7 rounded-full hover:bg-primary/10 text-muted-foreground cursor-pointer transition-colors">
                                <X class="h-3.5 w-3.5" />
                            </button>
                        </li>
                    </ul>

                    <!-- Can't decide recommendation fallback trigger -->
                    <div class="mt-5 border-t border-border pt-4">
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Can't decide?
                            <button @click="shortlistStore.items = []; step = 3;"
                                class="text-primary hover:underline cursor-pointer bg-transparent border-0 inline-block font-semibold">
                                Ask HMP Hospitality to recommend suitable options based on your brief.
                            </button>
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl bg-ink p-6 text-ink-fg">
                    <h3 class="font-heading text-xl text-white">Save & continue later</h3>
                    <p class="mt-3 text-sm text-ink-fg/70 leading-relaxed">Your shortlist is saved on this device. Come
                        back any
                        time to finish your RFP.</p>
                </div>
            </aside>
        </section>

        <!-- Form controls footer -->
        <section class="container-wide pb-16">
            <div class="flex items-center justify-between border-t border-border pt-6">
                <button @click="back" :disabled="step === 0"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground disabled:opacity-40 cursor-pointer">
                    <ArrowLeft class="h-4 w-4" /> Back
                </button>

                <button v-if="step < 3" @click="next" :disabled="!isValid"
                    class="btn-primary disabled:opacity-50 cursor-pointer">
                    Continue
                    <ArrowRight class="h-4 w-4" />
                </button>

                <button v-else @click="submit"
                    :disabled="!f.acceptPrivacy || (wantsAgency && !f.agencyConsent) || submitting"
                    class="btn-primary disabled:opacity-50 cursor-pointer">
                    {{ submitting ? 'Submitting...' : 'Submit RFP' }}
                    <ArrowRight class="h-4 w-4" />
                </button>
            </div>
        </section>

        <!-- Dynamic Success Modal overlay -->
        <div v-if="showSuccessModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div
                class="relative max-w-xl w-full rounded-3xl border border-border bg-card p-8 lg:p-12 text-center shadow-xl animate-in fade-in zoom-in-95 duration-200">
                <button @click="showSuccessModal = false"
                    class="absolute right-6 top-6 grid place-items-center h-8 w-8 rounded-full hover:bg-muted text-muted-foreground cursor-pointer border-0">
                    <X class="h-4 w-4" />
                </button>

                <span
                    class="grid place-items-center h-16 w-16 rounded-full bg-primary text-primary-foreground mx-auto shadow-sm">
                    <Check class="h-8 w-8" />
                </span>

                <h2 class="mt-6 font-heading text-3xl">Your RFP has been received.</h2>
                <p class="mt-4 text-sm text-muted-foreground leading-relaxed">
                    Thank you, {{ successName }}. HMP Hospitality will review your brief, match it to represented
                    members and
                    respond within one business day.
                </p>

                <div
                    class="mt-6 inline-flex flex-col items-center rounded-2xl border border-border bg-background px-8 py-4">
                    <span class="text-xs uppercase tracking-wider text-muted-foreground">Your RFP reference</span>
                    <span class="mt-1 font-heading text-xl text-primary font-bold">{{ successRef }}</span>
                </div>

                <p v-if="wantsAgency && f.agencyConsent" class="mt-4 text-xs text-muted-foreground leading-relaxed">
                    Your additional support request has been noted and shared with HMP Agency.
                </p>

                <div class="mt-8 flex flex-wrap gap-3 justify-center">
                    <Link href="/portfolio" @click="showSuccessModal = false" class="btn-primary">Back to portfolio
                    </Link>
                    <Link href="/" @click="showSuccessModal = false" class="btn-ghost">Return home</Link>
                </div>
            </div>
        </div>
    </div>
</template>