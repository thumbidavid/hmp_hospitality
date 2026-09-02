<script setup>
import { ref } from 'vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'

// Design-system components imported from your verified admin kit
import InputLabel from '@/Components/Admin/InputLabel.vue'
import InputError from '@/Components/Admin/InputError.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import TextArea from '@/Components/Admin/TextArea.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import Checkbox from '@/Components/Admin/Checkbox.vue'
import FileUploader from '@/Components/Admin/FileUploader.vue'

const props = defineProps({
    countries: Array
})

// Initialize form matching ResourceForm state structure
const form = useForm({
    country_id: '',
    name: '',
    slug: '',
    intro_description: '',
    access_connectivity: '',
    best_time_to_visit: '',
    website_url: '',
    hero_image: null, // Receives the Media ID returned by FileUploader on upload success
    is_featured: false,
    is_active: true,
    sort_order: 0,
    reasons: [],      // Array of { text: '', sort_order: 0 }
    attractions: [],  // Array of { text: '', sort_order: 0 }
    docs: []          // Array of { label: '', file_id: null, sort_order: 0 }
})

// Reasons to Visit Helpers
const addReason = () => {
    form.reasons.push({ text: '', sort_order: form.reasons.length })
}
const removeReason = (index) => {
    form.reasons.splice(index, 1)
}

// Experiences & Attractions Helpers
const addAttraction = () => {
    form.attractions.push({ text: '', sort_order: form.attractions.length })
}
const removeAttraction = (index) => {
    form.attractions.splice(index, 1)
}

// Downloadable Documents Helpers
const addDoc = () => {
    form.docs.push({ label: '', file_id: null, sort_order: form.docs.length })
}
const removeDoc = (index) => {
    form.docs.splice(index, 1)
}

const submit = () => {
    form.post(route('app.admin.destinations.store'), {
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Create Destination" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Create Destination" />

        <div class="mx-auto max-w-full pb-12">
            <!-- Back Navigation Control -->
            <div class="mb-6">
                <Link :href="route('app.admin.destinations.index')"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to Destinations
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                    <!-- Left Columns: Core Inputs -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Section 1: Identity & Location -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Identity & Location
                            </h4>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <InputLabel for="name" value="Destination Name" />
                                    <TextInput id="name" v-model="form.name" type="text" placeholder="e.g. Zanzibar"
                                        class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="slug" value="URL Slug (Optional)" />
                                    <TextInput id="slug" v-model="form.slug" type="text" placeholder="e.g. zanzibar"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.slug" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="country_id" value="Associated Country" />
                                    <SelectInput id="country_id" v-model="form.country_id" class="mt-1 block w-full"
                                        :options="countries.map(c => ({ value: c.id, label: `${c.name} (${c.region})` }))"
                                        required>
                                        <option value="" disabled>Select Associated Country</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.country_id" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Marketing Copy -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Descriptive Content
                            </h4>

                            <div class="space-y-5">
                                <div>
                                    <InputLabel for="intro_description" value="Intro & Overview Description" />
                                    <TextArea id="intro_description" v-model="form.intro_description"
                                        placeholder="Brief marketing introductory description..."
                                        class="mt-1 block w-full" rows="4" />
                                    <InputError class="mt-2" :message="form.errors.intro_description" />
                                </div>

                                <div>
                                    <InputLabel for="access_connectivity" value="Access & Connectivity" />
                                    <TextArea id="access_connectivity" v-model="form.access_connectivity"
                                        placeholder="Explain flight options, airport details, and ground connections..."
                                        class="mt-1 block w-full" rows="3" />
                                    <InputError class="mt-2" :message="form.errors.access_connectivity" />
                                </div>

                                <div>
                                    <InputLabel for="best_time_to_visit" value="Best Time to Visit" />
                                    <TextArea id="best_time_to_visit" v-model="form.best_time_to_visit"
                                        placeholder="Seasonal recommendations, climate cycles, and peak business periods..."
                                        class="mt-1 block w-full" rows="3" />
                                    <InputError class="mt-2" :message="form.errors.best_time_to_visit" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Media & Configuration -->
                    <div class="space-y-6">

                        <!-- Section 3: Media Upload -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Hero Image</h4>

                            <div class="space-y-2">
                                <InputLabel for="hero_image" value="Cover Media File" />
                                <FileUploader id="hero_image" v-model="form.hero_image" class="mt-1" />
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                            </div>
                        </div>

                        <!-- Section 4: Configuration Settings -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Settings &
                                Visibility</h4>

                            <div class="space-y-5">
                                <div>
                                    <InputLabel for="website_url" value="Official DMO Website Link" />
                                    <TextInput id="website_url" v-model="form.website_url" type="url"
                                        placeholder="https://example.com" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.website_url" />
                                </div>

                                <div>
                                    <InputLabel for="sort_order" value="Sorting Index" />
                                    <TextInput id="sort_order" v-model="form.sort_order" type="number" placeholder="0"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.sort_order" />
                                </div>

                                <div class="flex flex-col gap-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                                    <Checkbox id="is_active" v-model="form.is_active" label="Publish Member">
                                        Display this destination on the public portal.
                                    </Checkbox>

                                    <Checkbox id="is_featured" v-model="form.is_featured" label="Promote on Homepage">
                                        Feature this destination in the homepage carousel.
                                    </Checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Dynamic Attribute Lists -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">

                    <!-- Reasons to Visit -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Reasons to Visit</h4>
                            <button type="button" @click="addReason"
                                class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Add Point
                            </button>
                        </div>

                        <div v-if="form.reasons.length === 0"
                            class="flex h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 text-gray-400 dark:border-gray-800">
                            <span class="material-symbols-outlined mb-1 text-2xl">help_center</span>
                            <span class="text-xs font-medium">No reasons specified yet</span>
                        </div>

                        <ul v-else class="space-y-4">
                            <li v-for="(reason, index) in form.reasons" :key="index" class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <InputLabel :for="`reason_${index}`" :value="`Reason #${index + 1}`" />
                                    <TextInput :id="`reason_${index}`" v-model="reason.text"
                                        placeholder="e.g. Robust local logistics infrastructure"
                                        class="mt-1 block w-full" required />
                                </div>
                                <div class="w-16">
                                    <InputLabel :for="`reason_sort_${index}`" value="Sort" />
                                    <TextInput :id="`reason_sort_${index}`" v-model="reason.sort_order" type="number"
                                        class="mt-1 block w-full text-center" />
                                </div>
                                <button type="button" @click="removeReason(index)"
                                    class="mb-2 text-gray-400 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Local Attractions -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Experiences &
                                Attractions</h4>
                            <button type="button" @click="addAttraction"
                                class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Add Attraction
                            </button>
                        </div>

                        <div v-if="form.attractions.length === 0"
                            class="flex h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 text-gray-400 dark:border-gray-800">
                            <span class="material-symbols-outlined mb-1 text-2xl">tour</span>
                            <span class="text-xs font-medium">No local experiences specified yet</span>
                        </div>

                        <ul v-else class="space-y-4">
                            <li v-for="(attraction, index) in form.attractions" :key="index"
                                class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <InputLabel :for="`attraction_${index}`" :value="`Attraction #${index + 1}`" />
                                    <TextInput :id="`attraction_${index}`" v-model="attraction.text"
                                        placeholder="e.g. Stone Town historical architecture walk"
                                        class="mt-1 block w-full" required />
                                </div>
                                <div class="w-16">
                                    <InputLabel :for="`attraction_sort_${index}`" value="Sort" />
                                    <TextInput :id="`attraction_sort_${index}`" v-model="attraction.sort_order"
                                        type="number" class="mt-1 block w-full text-center" />
                                </div>
                                <button type="button" @click="removeAttraction(index)"
                                    class="mb-2 text-gray-400 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Section: Media Document Attachments -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Documents & Guides</h4>
                            <p class="text-xs text-gray-400">Attach collateral files (Destination PDF guides, event
                                planning briefs, etc.).</p>
                        </div>
                        <button type="button" @click="addDoc"
                            class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                            <span class="material-symbols-outlined text-sm">add_circle</span> Add Document
                        </button>
                    </div>

                    <div v-if="form.docs.length === 0"
                        class="flex h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 text-gray-400 dark:border-gray-800">
                        <span class="material-symbols-outlined mb-1 text-2xl">picture_as_pdf</span>
                        <span class="text-xs font-medium">No documents attached yet</span>
                    </div>

                    <ul v-else class="space-y-6">
                        <li v-for="(doc, index) in form.docs" :key="index"
                            class="flex flex-wrap gap-5 items-end border-b border-gray-100 pb-5 last:border-0 last:pb-0 dark:border-gray-800/50">
                            <div class="flex-1 min-w-[240px]">
                                <InputLabel :for="`doc_label_${index}`" value="Document Label" />
                                <TextInput :id="`doc_label_${index}`" v-model="doc.label"
                                    placeholder="e.g. Zanzibar destination Guide (2026)" class="mt-1 block w-full"
                                    required />
                            </div>

                            <div class="w-80 min-w-[240px]">
                                <InputLabel :for="`doc_file_${index}`" value="Upload File Attachment" />
                                <FileUploader :id="`doc_file_${index}`" v-model="doc.file_id" class="mt-1" />
                            </div>

                            <div class="w-16">
                                <InputLabel :for="`doc_sort_${index}`" value="Sort" />
                                <TextInput :id="`doc_sort_${index}`" v-model="doc.sort_order" type="number"
                                    class="mt-1 block w-full text-center" />
                            </div>

                            <div class="pb-2">
                                <button type="button" @click="removeDoc(index)"
                                    class="text-gray-400 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Form Controls -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <Link :href="route('app.admin.destinations.index')"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950">
                        Cancel
                    </Link>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Destination</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
