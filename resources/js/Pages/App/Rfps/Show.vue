<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'

const props = defineProps({
    rfp: Object
})

// Form instance to handle state changes on Pipeline Status
const statusForm = useForm({
    status: props.rfp.status
})

// Form instance to handle posting internal pipeline notes
const noteForm = useForm({
    note: ''
})

const updateStatus = () => {
    statusForm.put(route('app.admin.rfps.update', props.rfp.id), {
        preserveScroll: true
    })
}

const submitNote = () => {
    noteForm.post(route('app.admin.rfps.notes.store', props.rfp.id), {
        preserveScroll: true,
        onSuccess: () => {
            noteForm.reset()
        }
    })
}

const formatDate = (dateString) => {
    if (!dateString) return 'TBD'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const formatDateTime = (timeString) => {
    if (!timeString) return '-'
    const date = new Date(timeString)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatCurrency = (amount, currency) => {
    if (!amount) return 'TBD'
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD' }).format(amount)
}

const typeMap = {
    business_travel: 'Business Travel Engagement',
    group_accommodation: 'Group Accommodation Block',
    conference_or_meeting: 'Conference & Event Facilities',
    incentive_programme: 'Incentive Travel Programme',
    association_event: 'Association Conference',
    government_ngo_programme: 'Gov / NGO Operational Support',
    leisure_group: 'Leisure Group Blocks',
    long_stay_accommodation: 'Long-Stay Residental Suites',
    venue_only_event: 'Venue-Only Facility Rental',
    destination_enquiry: 'Destination Representation Inquiry'
}
</script>

<template>

    <Head :title="`RFP Lead ${rfp.reference_number}`" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Review Lead Details" />

        <div class="mx-auto max-w-full pb-16">
            <!-- Navigation controls -->
            <div class="mb-6">
                <Link :href="route('app.admin.rfps.index')"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to Lead Board
                </Link>
            </div>

            <!-- Dashboard Split Layout -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                <!-- Left Columns: Full Client Specs -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Section 1: Lead Header -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span class="font-mono text-xs font-bold text-brand-500">{{ rfp.reference_number
                                    }}</span>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ rfp.programme_name || 'General Portfolio Request' }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-1">Submitted: {{ formatDateTime(rfp.created_at) }}
                                    via {{ rfp.source }}</p>
                            </div>

                            <!-- Static Inquiry Badge -->
                            <span
                                class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                {{ typeMap[rfp.requirement_type] || rfp.requirement_type }}
                            </span>
                        </div>
                    </div>

                    <!-- Section 2: Buyer Profile -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Contact & Buyer
                            Profile</h4>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Contact Name</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ rfp.full_name }}</span>
                                <span v-if="rfp.job_title" class="block text-xs text-gray-400">({{ rfp.job_title
                                    }})</span>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Company /
                                    Organization</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ rfp.company_name ||
                                    'Independent Lead' }}</span>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Email Address</span>
                                <a :href="`mailto:${rfp.email}`"
                                    class="font-medium text-brand-500 hover:underline inline-flex items-center gap-1">
                                    {{ rfp.email }}
                                    <span class="material-symbols-outlined text-xs">mail</span>
                                </a>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Phone Contact</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ rfp.phone || 'None provided' }}
                                </span>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Origin Details</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ rfp.buyer_country ? rfp.buyer_country.name : 'Unknown' }} ({{ rfp.buyer_type ?
                                        rfp.buyer_type.name : 'Other Buyer' }})
                                </span>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Preferred
                                    Outreach</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{
                                    rfp.preferred_communication_method.replace('_', ' ') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Event Specifications -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Programme
                            Specifications</h4>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Destination
                                    Location</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ rfp.destination ? rfp.destination.name : rfp.preferred_destination }}
                                </span>
                                <span v-if="rfp.is_destination_flexible"
                                    class="block text-xs text-amber-500 font-semibold">Flexible on Destination</span>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Travel Dates
                                    Block</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ formatDate(rfp.arrival_date) }} &rarr; {{ formatDate(rfp.departure_date) }}
                                </span>
                                <span v-if="rfp.is_dates_flexible"
                                    class="block text-xs text-amber-500 font-semibold">Flexible on Dates</span>
                            </div>

                            <div
                                class="grid grid-cols-3 gap-2 sm:col-span-2 border-y border-gray-50 py-3 dark:border-gray-800">
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Attendees</span>
                                    <span class="text-base font-bold text-gray-800 dark:text-gray-200">{{
                                        rfp.number_of_attendees || 'TBD' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Guestrooms</span>
                                    <span class="text-base font-bold text-gray-800 dark:text-gray-200">{{
                                        rfp.number_of_rooms || 'TBD' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Room Nights</span>
                                    <span class="text-base font-bold text-gray-800 dark:text-gray-200">{{
                                        rfp.number_of_room_nights || 'TBD' }}</span>
                                </div>
                            </div>

                            <div v-if="rfp.meeting_room_requirements" class="sm:col-span-2">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Meetings &
                                    Spaces</span>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">{{ rfp.meeting_room_requirements }}</p>
                                <span v-if="rfp.venue_capacity"
                                    class="text-xs text-gray-400 font-semibold block mt-1">Requires venue capacity of {{
                                        rfp.venue_capacity }} pax</span>
                            </div>

                            <div v-if="rfp.fnb_requirements" class="sm:col-span-2">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Food & Beverage
                                    (F&B)</span>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">{{ rfp.fnb_requirements }}</p>
                            </div>

                            <div v-if="rfp.transfer_airport_requirements" class="sm:col-span-2">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">Ground
                                    Transfers</span>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">{{ rfp.transfer_airport_requirements }}
                                </p>
                            </div>

                            <div
                                class="sm:col-span-2 grid grid-cols-2 gap-4 border-t border-gray-50 pt-4 dark:border-gray-800">
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Estimated
                                        Budget</span>
                                    <span class="text-base font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(rfp.budget_amount, rfp.currency) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Key Decision
                                        Date</span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ formatDate(rfp.decision_date) }}
                                    </span>
                                    <span class="text-xs text-gray-400 block" v-if="rfp.proposal_deadline">Proposal
                                        deadline: {{ formatDate(rfp.proposal_deadline) }}</span>
                                </div>
                            </div>

                            <div v-if="rfp.attachment_url"
                                class="sm:col-span-2 border-t border-gray-50 pt-4 dark:border-gray-800">
                                <span class="block text-xs font-semibold text-gray-400 uppercase mb-2">Uploaded Brief
                                    File</span>
                                <a :href="rfp.attachment_url" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-750">
                                    <span class="material-symbols-outlined text-sm">download</span>
                                    Download Attachment / Brief
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Shortlisted Properties -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Shortlisted Collection
                            Members</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="prop in rfp.properties" :key="prop.id"
                                class="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950 flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 rounded-lg overflow-hidden bg-gray-200">
                                    <img v-if="prop.featured_image_url" :src="prop.featured_image_url"
                                        class="h-full w-full object-cover" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="block font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">{{
                                            prop.name }}</span>
                                    <span class="block text-xs text-gray-400 truncate">{{ prop.city }} ({{
                                        prop.destination ? prop.destination.name : 'Unknown' }})</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Requested Cross-Sells -->
                    <div v-if="rfp.agency_services.length > 0"
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Requested Agency
                            Support Cross-Sells</h4>
                        <p class="text-xs text-gray-400 mb-4">The client checked these business travel or event delivery
                            assistance services (HMP Agency sister unit):</p>

                        <div class="flex flex-wrap gap-2">
                            <span v-for="service in rfp.agency_services" :key="service.id"
                                class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                {{ service.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Status & Notes Thread -->
                <div class="space-y-6">

                    <!-- Section 6: Pipeline Status controller -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Pipeline Management
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-400 uppercase">Change Lead
                                    Status</label>
                                <select v-model="statusForm.status" @change="updateStatus"
                                    :disabled="statusForm.processing"
                                    class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100">
                                    <option value="new">New Lead</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="quoted">Quoted / Proposed</option>
                                    <option value="won">Won / Contracted</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Communication Notes Feed -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Internal Pipeline
                            Notes</h4>

                        <!-- Create Note Form -->
                        <form @submit.prevent="submitNote" class="space-y-3">
                            <div>
                                <textarea v-model="noteForm.note"
                                    placeholder="Write internal updates here (e.g. called client, sent proposal)..."
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100"
                                    required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton type="submit" :disabled="noteForm.processing" class="py-1 px-3 text-xs">
                                    {{ noteForm.processing ? 'Adding...' : 'Add Note' }}
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Notes Feed List -->
                        <div class="border-t border-gray-100 mt-6 pt-6 dark:border-gray-800">
                            <h5 class="text-xs font-semibold text-gray-400 uppercase mb-4">Activity Log</h5>

                            <div v-if="rfp.notes.length === 0"
                                class="text-center py-6 text-xs text-gray-400 font-semibold italic">
                                No internal notes recorded yet.
                            </div>

                            <ul v-else class="space-y-4 max-h-[350px] overflow-y-auto no-scrollbar pr-1">
                                <li v-for="note in rfp.notes" :key="note.id"
                                    class="rounded-xl bg-gray-50 p-3 text-xs dark:bg-gray-950 flex flex-col gap-1 border border-gray-100/50 dark:border-gray-800/50">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ note.user ?
                                            note.user.name : 'Unknown Staff' }}</span>
                                        <span class="text-gray-400 font-medium">{{ formatDateTime(note.created_at)
                                            }}</span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{{ note.note }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
