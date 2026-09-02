<script setup>
import { ref, reactive, computed } from "vue"
import { router, usePage } from "@inertiajs/vue3" // Import usePage
import { Search, X, Minus, Plus, ChevronRight } from "lucide-vue-next"

const requirementOptions = [
    "Business Travel", "Individual Accommodation", "Group Accommodation",
    "Conference or Meeting", "Corporate Event", "Association Event",
    "Incentive Programme", "Government or NGO Programme", "Leisure Group",
    "Long-stay Accommodation", "Venue-only Event", "Destination Enquiry"
]

const ptypeOptions = [
    "All Property Types", "Hotel", "Resort", "Safari Lodge/Camp",
    "Conference Venue", "Unique Venue", "Serviced Residence", "Destination or DMO"
]

const accReqs = [
    "Business Travel", "Individual Accommodation",
    "Group Accommodation", "Leisure Group", "Long-stay Accommodation"
]

const eventReqs = [
    "Conference or Meeting", "Corporate Event", "Association Event",
    "Incentive Programme", "Government or NGO Programme", "Venue-only Event"
]

const page = usePage()

// Read from dynamic page props with a fallback list so compilation passes during initial setup
const destTokens = computed(() => {
    return page.props.destTokens || ["Kenya", "South Africa", "Nairobi", "Cape Town"]
})

const open = ref(false)

const f = reactive({
    destination: "",
    ptype: "All Property Types",
    requirement: "",
    guests: 0,
    rooms: 0,
    checkin: "",
    checkout: "",
    eventStart: "",
    eventEnd: ""
})

const counter = (k, d) => {
    f[k] = Math.max(0, (f[k] || 0) + d)
}

const needsAcc = computed(() => accReqs.includes(f.requirement))
const needsEvent = computed(() => eventReqs.includes(f.requirement))

const datesValid = computed(() => {
    const accCondition = !needsAcc.value || (f.checkin && f.checkout && f.checkout > f.checkin)
    const eventCondition = !needsEvent.value || (f.eventStart && f.eventEnd && f.eventEnd > f.eventStart)
    return accCondition && eventCondition
})

const buildParams = () => {
    const p = new URLSearchParams()
    if (f.destination) p.set("q", f.destination)
    if (f.ptype && f.ptype !== "All Property Types") {
        if (f.ptype === "Destination or DMO") {
            p.set("cat", "Destinations & DMOs")
        } else {
            p.set("type", f.ptype)
        }
    }
    if (f.requirement) p.set("req", f.requirement)
    if (f.guests > 0) p.set("minCap", f.guests)
    if (f.rooms > 0) p.set("minRooms", f.rooms)
    if (f.checkin) p.set("checkin", f.checkin)
    if (f.checkout) p.set("checkout", f.checkout)
    if (f.eventStart) p.set("eventStart", f.eventStart)
    if (f.eventEnd) p.set("eventEnd", f.eventEnd)
    return p.toString()
}

const submit = () => {
    if (!datesValid.value) return
    router.visit(`/portfolio?${buildParams()}`)
    open.value = false
}

const ctl = "w-full bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none cursor-pointer"
</script>

<template>
    <!-- Desktop / laptop booking bar -->
    <div
        class="hidden lg:block rounded-2xl bg-card/95 backdrop-blur-xl border border-ink/10 shadow-[0_40px_80px_-40px_rgba(20,14,8,0.45)] overflow-hidden">
        <div class="flex items-center gap-2 px-4 pt-4 pb-1 text-sm font-medium text-ink">
            <Search class="h-4 w-4 text-primary" /> Find the Right Property
        </div>

        <!-- Row 1 -->
        <div class="grid grid-cols-3 divide-x divide-ink/10">
            <!-- Cell: Destination -->
            <div class="px-4 py-3 lg:py-3.5">
                <label
                    class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">Destination</label>
                <input list="dest-list-d" v-model="f.destination" placeholder="Where are you going?"
                    class="w-full bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none" />
                <datalist id="dest-list-d">
                    <option v-for="t in destTokens" :key="t" :value="t" />
                </datalist>
            </div>

            <!-- Cell: Property Type -->
            <div class="px-4 py-3 lg:py-3.5">
                <label
                    class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">Property
                    Type</label>
                <select v-model="f.ptype" :class="ctl">
                    <option v-for="o in ptypeOptions" :key="o" :value="o">{{ o }}</option>
                </select>
            </div>

            <!-- Cell: Requirement -->
            <div class="px-4 py-3 lg:py-3.5">
                <label
                    class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">Requirement</label>
                <select v-model="f.requirement" :class="ctl">
                    <option value="">What are you planning?</option>
                    <option v-for="o in requirementOptions" :key="o" :value="o">{{ o }}</option>
                </select>
            </div>
        </div>

        <div class="h-px bg-ink/10 mx-4" />

        <!-- Row 2 -->
        <div class="grid grid-cols-3 divide-x divide-ink/10">
            <!-- Cell: Guests / Rooms -->
            <div class="px-4 py-3 lg:py-3.5">
                <label
                    class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">Guests
                    / Rooms</label>
                <div class="flex items-center gap-2 min-w-0">
                    <!-- Guests Stepper -->
                    <div
                        class="flex items-center justify-between flex-1 min-w-0 rounded-lg border border-ink/10 bg-background px-2.5 py-1.5">
                        <span class="text-[11px] text-muted-foreground truncate">Guests</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="counter('guests', -1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Minus class="h-3 w-3" />
                            </button>
                            <span class="w-6 text-center text-sm font-medium">{{ f.guests }}</span>
                            <button type="button" @click="counter('guests', 1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Plus class="h-3 w-3" />
                            </button>
                        </div>
                    </div>

                    <!-- Rooms Stepper -->
                    <div
                        class="flex items-center justify-between flex-1 min-w-0 rounded-lg border border-ink/10 bg-background px-2.5 py-1.5">
                        <span class="text-[11px] text-muted-foreground truncate">Rooms</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="counter('rooms', -1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Minus class="h-3 w-3" />
                            </button>
                            <span class="w-6 text-center text-sm font-medium">{{ f.rooms }}</span>
                            <button type="button" @click="counter('rooms', 1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Plus class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cell: Dates Accommodation -->
            <div class="px-4 py-3 lg:py-3.5">
                <label class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">
                    Check-in / Check-out<span v-if="needsAcc" class="text-primary"> *</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" v-model="f.checkin"
                        class="rounded-lg border border-ink/10 bg-background px-2 py-1.5 text-sm focus:outline-none focus:border-primary" />
                    <input type="date" v-model="f.checkout" :min="f.checkin"
                        class="rounded-lg border border-ink/10 bg-background px-2 py-1.5 text-sm focus:outline-none focus:border-primary" />
                </div>
            </div>

            <!-- Cell: Dates Events -->
            <div class="px-4 py-3 lg:py-3.5">
                <label class="block text-[10px] font-medium uppercase tracking-[0.18em] text-muted-foreground mb-1.5">
                    Event Start / Event End<span v-if="needsEvent" class="text-primary"> *</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="datetime-local" v-model="f.eventStart"
                        class="rounded-lg border border-ink/10 bg-background px-2 py-1.5 text-sm focus:outline-none focus:border-primary" />
                    <input type="datetime-local" v-model="f.eventEnd" :min="f.eventStart"
                        class="rounded-lg border border-ink/10 bg-background px-2 py-1.5 text-sm focus:outline-none focus:border-primary" />
                </div>
            </div>
        </div>

        <p v-if="f.requirement && !datesValid" class="px-4 pb-2 text-xs text-primary">
            {{ needsAcc ? "Please select check-in and check-out dates." : "Please select event start and end dates." }}
        </p>

        <button @click="submit" :disabled="!!f.requirement && !datesValid"
            class="w-full bg-primary text-white text-sm font-medium py-3.5 hover:bg-ink transition-colors disabled:opacity-50 cursor-pointer">
            Search Properties
        </button>
    </div>

    <!-- Tablet / mobile trigger -->
    <div class="lg:hidden">
        <button @click="open = true"
            class="w-full flex items-center justify-between rounded-2xl bg-card/95 backdrop-blur-xl border border-ink/10 px-5 py-4 shadow-[0_30px_60px_-35px_rgba(20,14,8,0.4)] cursor-pointer">
            <span class="flex items-center gap-2 text-sm font-medium text-ink">
                <Search class="h-4 w-4 text-primary" /> Find a Property
            </span>
            <ChevronRight class="h-5 w-5 text-primary" />
        </button>
    </div>

    <!-- Full-screen Mobile Sheet -->
    <div v-if="open" class="lg:hidden fixed inset-0 z-[60] bg-background flex flex-col">
        <!-- Mobile Sheet Header -->
        <div class="flex items-center justify-between px-5 h-16 border-b border-border bg-card">
            <span class="flex items-center gap-2 text-sm font-medium text-ink">
                <Search class="h-4 w-4 text-primary" /> Find the Right Property
            </span>
            <button @click="open = false" class="grid place-items-center h-10 w-10 rounded-full border border-border"
                aria-label="Close">
                <X class="h-5 w-5" />
            </button>
        </div>

        <!-- Mobile Form Content -->
        <div class="flex-1 overflow-y-auto px-5 py-6 space-y-5">
            <!-- Mobile Destination -->
            <div>
                <label
                    class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Destination</label>
                <input list="dest-list-m" v-model="f.destination" placeholder="Where are you going?"
                    class="input-field" />
                <datalist id="dest-list-m">
                    <option v-for="t in destTokens" :key="t" :value="t" />
                </datalist>
            </div>

            <!-- Mobile Property Type -->
            <div>
                <label
                    class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Property
                    Type</label>
                <select v-model="f.ptype" class="input-field cursor-pointer">
                    <option v-for="o in ptypeOptions" :key="o" :value="o">{{ o }}</option>
                </select>
            </div>

            <!-- Mobile Requirement -->
            <div>
                <label
                    class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Requirement</label>
                <select v-model="f.requirement" class="input-field cursor-pointer">
                    <option value="">What are you planning?</option>
                    <option v-for="o in requirementOptions" :key="o" :value="o">{{ o }}</option>
                </select>
            </div>

            <!-- Mobile Guests / Rooms -->
            <div>
                <label
                    class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">Guests /
                    Rooms</label>
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center justify-between flex-1 min-w-0 rounded-lg border border-ink/10 bg-background px-2.5 py-1.5">
                        <span class="text-[11px] text-muted-foreground truncate">Guests</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="counter('guests', -1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Minus class="h-3 w-3" />
                            </button>
                            <span class="w-6 text-center text-sm font-medium">{{ f.guests }}</span>
                            <button type="button" @click="counter('guests', 1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Plus class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between flex-1 min-w-0 rounded-lg border border-ink/10 bg-background px-2.5 py-1.5">
                        <span class="text-[11px] text-muted-foreground truncate">Rooms</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="counter('rooms', -1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Minus class="h-3 w-3" />
                            </button>
                            <span class="w-6 text-center text-sm font-medium">{{ f.rooms }}</span>
                            <button type="button" @click="counter('rooms', 1)"
                                class="grid place-items-center h-6 w-6 rounded-full border border-ink/10 hover:border-primary">
                                <Plus class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Check-in / Check-out -->
            <div>
                <label class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">
                    Check-in / Check-out<span v-if="needsAcc" class="text-primary"> *</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <input type="date" v-model="f.checkin" class="input-field" />
                    <input type="date" v-model="f.checkout" :min="f.checkin" class="input-field" />
                </div>
            </div>

            <!-- Mobile Event Start / Event End -->
            <div>
                <label class="block text-[11px] font-medium uppercase tracking-wider text-muted-foreground mb-1.5">
                    Event Start / Event End<span v-if="needsEvent" class="text-primary"> *</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <input type="datetime-local" v-model="f.eventStart" class="input-field" />
                    <input type="datetime-local" v-model="f.eventEnd" :min="f.eventStart" class="input-field" />
                </div>
            </div>

            <p v-if="f.requirement && !datesValid" class="text-xs text-primary">
                {{ needsAcc ? "Please select check-in and check-out dates." : "Please select event start and end dates."
                }}
            </p>
        </div>

        <!-- Mobile Submit Button Footer -->
        <div class="px-5 py-4 border-t border-border bg-card">
            <button @click="submit" :disabled="!!f.requirement && !datesValid"
                class="btn-primary w-full cursor-pointer disabled:opacity-50">
                Search Properties
            </button>
        </div>
    </div>
</template>