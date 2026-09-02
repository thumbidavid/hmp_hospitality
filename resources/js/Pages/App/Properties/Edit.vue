<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'

// Import design-system form components
import InputLabel from '@/Components/Admin/InputLabel.vue'
import InputError from '@/Components/Admin/InputError.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import TextArea from '@/Components/Admin/TextArea.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import Checkbox from '@/Components/Admin/Checkbox.vue'
import FileUploader from '@/Components/Admin/FileUploader.vue'

const props = defineProps({
    property: Object,
    categories: Array,
    destinations: Array,
    countries: Array,
    settings: Array,
    amenities: Array
})

// Setup preloaded form data from the backend property relations
const form = useForm({
    portfolio_category_id: props.property.portfolio_category_id,
    destination_id: props.property.destination_id,
    country_id: props.property.country_id,
    name: props.property.name,
    slug: props.property.slug,
    tagline: props.property.tagline || '',
    overview: props.property.overview || '',
    accommodation_details: props.property.accommodation_details || '',
    meetings_facilities_details: props.property.meetings_facilities_details || '',
    dining_leisure_details: props.property.dining_leisure_details || '',
    sustainability_details: props.property.sustainability_details || '',
    city: props.property.city || '',
    latitude: props.property.latitude || '',
    longitude: props.property.longitude || '',
    number_of_rooms: props.property.number_of_rooms || '',
    max_event_capacity: props.property.max_event_capacity || '',
    website_url: props.property.website_url || '',
    featured_image: null, // Populated with a new Media ID only if replaced
    is_featured: !!props.property.is_featured,
    is_active: !!props.property.is_active,
    sort_order: props.property.sort_order || 0,

    // Map loaded pivot associations to reactive arrays
    settings: props.property.settings.map(s => s.id),
    amenities: props.property.amenities.map(a => a.id),

    // Map child highlights to reactive repeaters
    key_experiences: props.property.highlights
        .filter(h => h.type === 'key_experience')
        .map(h => ({ text: h.text, sort_order: h.sort_order })),

    // Map gallery images
    gallery: props.property.images.map(img => ({
        id: img.id,
        media_id: null,
        image_url: img.image_url,
        caption: img.caption || '',
        is_cover: !!img.is_cover,
        sort_order: img.sort_order
    })),

    // Map downloadable guide documents
    docs: props.property.documents.map(d => ({
        id: d.id,
        label: d.label,
        file_id: null,
        file_url: d.file_url,
        sort_order: d.sort_order
    }))
})

// Key Experiences Helpers
const addExperience = () => {
    form.key_experiences.push({ text: '', sort_order: form.key_experiences.length })
}
const removeExperience = (index) => {
    form.key_experiences.splice(index, 1)
}

// Downloadable Documents Helpers
const addDoc = () => {
    form.docs.push({ label: '', file_id: null, file_url: null, sort_order: form.docs.length })
}
const removeDoc = (index) => {
    form.docs.splice(index, 1)
}

// Photo Gallery Helpers
const addGalleryImage = () => {
    form.gallery.push({ media_id: null, image_url: null, caption: '', is_cover: false, sort_order: form.gallery.length })
}
const removeGalleryImage = (index) => {
    form.gallery.splice(index, 1)
}

// Auto-fill country field based on the chosen destination
const handleDestinationChange = () => {
    const selectedDest = props.destinations.find(d => d.id === parseInt(form.destination_id))
    if (selectedDest) {
        form.country_id = selectedDest.country_id
    }
}

const submit = () => {
    form.put(route('app.admin.properties.update', props.property.id), {
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Edit Property" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Edit Property" />

        <div class="mx-auto max-w-full pb-16">
            <!-- Back navigation links -->
            <div class="mb-6">
                <Link :href="route('app.admin.properties.index')"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to Properties
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Basic Info & Relationships -->
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Basic Information
                            </h4>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <InputLabel for="name" value="Property / Venue Name" />
                                    <TextInput id="name" v-model="form.name" type="text"
                                        placeholder="e.g. Sana Beach Lodge" class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="slug" value="URL Slug" />
                                    <TextInput id="slug" v-model="form.slug" type="text"
                                        placeholder="e.g. sana-beach-lodge" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.slug" />
                                </div>

                                <div>
                                    <InputLabel for="tagline" value="Short Tagline" />
                                    <TextInput id="tagline" v-model="form.tagline" type="text"
                                        placeholder="e.g. A barefoot-luxury oceanfront experience"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.tagline" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="overview" value="Property Overview Description" />
                                    <TextArea id="overview" v-model="form.overview"
                                        placeholder="Provide a general summary intro to this represented member..."
                                        class="mt-1 block w-full" rows="3" />
                                    <InputError class="mt-2" :message="form.errors.overview" />
                                </div>
                            </div>
                        </div>

                        <!-- System Categorizations & Geography -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Classification &
                                Location</h4>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                <div>
                                    <InputLabel for="portfolio_category_id" value="Portfolio Classification" />
                                    <SelectInput id="portfolio_category_id" v-model="form.portfolio_category_id"
                                        class="mt-1 block w-full" required>
                                        <option value="" disabled>Select Classification</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.portfolio_category_id" />
                                </div>

                                <div>
                                    <InputLabel for="destination_id" value="Parent Destination" />
                                    <SelectInput id="destination_id" v-model="form.destination_id"
                                        @change="handleDestinationChange" class="mt-1 block w-full" required>
                                        <option value="" disabled>Select Destination</option>
                                        <option v-for="dest in destinations" :key="dest.id" :value="dest.id">{{
                                            dest.name }}</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.destination_id" />
                                </div>

                                <div>
                                    <InputLabel for="country_id" value="Country" />
                                    <SelectInput id="country_id" v-model="form.country_id" class="mt-1 block w-full"
                                        required>
                                        <option value="" disabled>Select Country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">{{
                                            country.name }}</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.country_id" />
                                </div>

                                <div>
                                    <InputLabel for="city" value="City / Locality" />
                                    <TextInput id="city" v-model="form.city" type="text" placeholder="e.g. Nungwi"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.city" />
                                </div>

                                <div>
                                    <InputLabel for="latitude" value="Geographic Latitude" />
                                    <TextInput id="latitude" v-model="form.latitude" placeholder="e.g. -5.723145"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.latitude" />
                                </div>

                                <div>
                                    <InputLabel for="longitude" value="Geographic Longitude" />
                                    <TextInput id="longitude" v-model="form.longitude" placeholder="e.g. 39.298144"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.longitude" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Media, Settings, Visibility -->
                    <div class="space-y-6">
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Card Cover Image
                            </h4>
                            <FileUploader id="featured_image" v-model="form.featured_image"
                                :initial-file-url="property.featured_image_url" />
                            <InputError class="mt-2" :message="form.errors.featured_image" />
                        </div>

                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Specifications &
                                Status</h4>

                            <div class="space-y-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="number_of_rooms" value="Total Guestrooms" />
                                        <TextInput id="number_of_rooms" v-model="form.number_of_rooms" type="number"
                                            placeholder="e.g. 48" class="mt-1 block w-full" />
                                        <InputError class="mt-2" :message="form.errors.number_of_rooms" />
                                    </div>
                                    <div>
                                        <InputLabel for="max_event_capacity" value="Max Event Capacity" />
                                        <TextInput id="max_event_capacity" v-model="form.max_event_capacity"
                                            type="number" placeholder="e.g. 150" class="mt-1 block w-full" />
                                        <InputError class="mt-2" :message="form.errors.max_event_capacity" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="website_url" value="Official Property Website" />
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
                                    <Checkbox id="is_active" v-model="form.is_active" label="Active Member">
                                        Show this property publicly on the site.
                                    </Checkbox>

                                    <Checkbox id="is_featured" v-model="form.is_featured" label="Featured Member">
                                        Feature this property in our priority lists.
                                    </Checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pivot Selections: Settings & Amenities -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Environment / Best For Tags -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Environment Settings
                            ("Best For")</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="setting in settings" :key="setting.id" class="flex items-center gap-2">
                                <input type="checkbox" :id="'setting_' + setting.id" :value="setting.id"
                                    v-model="form.settings"
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-950" />
                                <InputLabel :for="'setting_' + setting.id" :value="setting.name"
                                    class="cursor-pointer" />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.settings" />
                    </div>

                    <!-- Property Amenities -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                        <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Property Amenities
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-for="amenity in amenities" :key="amenity.id" class="flex items-center gap-2">
                                <input type="checkbox" :id="'amenity_' + amenity.id" :value="amenity.id"
                                    v-model="form.amenities"
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-950" />
                                <InputLabel :for="'amenity_' + amenity.id" :value="amenity.name"
                                    class="cursor-pointer" />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.amenities" />
                    </div>
                </div>

                <!-- Structured Property Content Blocks -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                    <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Content Tab Sections</h4>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel for="accommodation_details" value="Accommodation Details" />
                            <TextArea id="accommodation_details" v-model="form.accommodation_details"
                                placeholder="Describe room configurations, layout sizing, and bed configurations..."
                                class="mt-1 block w-full" rows="3" />
                        </div>
                        <div>
                            <InputLabel for="meetings_facilities_details" value="Meetings & Events Facilities" />
                            <TextArea id="meetings_facilities_details" v-model="form.meetings_facilities_details"
                                placeholder="List banquet spaces, layout configurations, and conference tech specs..."
                                class="mt-1 block w-full" rows="3" />
                        </div>
                        <div>
                            <InputLabel for="dining_leisure_details" value="Dining, Leisure & Spa Details" />
                            <TextArea id="dining_leisure_details" v-model="form.dining_leisure_details"
                                placeholder="Describe restaurants, bar spaces, massage rooms, and pools..."
                                class="mt-1 block w-full" rows="3" />
                        </div>
                        <div>
                            <InputLabel for="sustainability_details" value="Sustainability & Community Details" />
                            <TextArea id="sustainability_details" v-model="form.sustainability_details"
                                placeholder="Detail environmental initiatives, water recycling, and community support actions..."
                                class="mt-1 block w-full" rows="3" />
                        </div>
                    </div>
                </div>

                <!-- Key Experiences Repeaters -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Key Experiences</h4>
                        <button type="button" @click="addExperience"
                            class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                            <span class="material-symbols-outlined text-sm">add_circle</span> Add Point
                        </button>
                    </div>

                    <div v-if="form.key_experiences.length === 0"
                        class="flex h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 text-gray-400 dark:border-gray-800">
                        <span class="material-symbols-outlined mb-1 text-2xl">star</span>
                        <span class="text-xs font-medium">No highlights configured yet</span>
                    </div>

                    <ul v-else class="space-y-4">
                        <li v-for="(experience, index) in form.key_experiences" :key="index"
                            class="flex gap-2 items-end">
                            <div class="flex-1">
                                <InputLabel :for="`exp_${index}`" :value="`Experience Bullet #${index + 1}`" />
                                <TextInput :id="`exp_${index}`" v-model="experience.text"
                                    placeholder="e.g. Private beach dinners under the equatorial stars"
                                    class="mt-1 block w-full" required />
                            </div>
                            <div class="w-16">
                                <InputLabel :for="`exp_sort_${index}`" value="Sort" />
                                <TextInput :id="`exp_sort_${index}`" v-model="experience.sort_order" type="number"
                                    class="mt-1 block w-full text-center" />
                            </div>
                            <button type="button" @click="removeExperience(index)"
                                class="text-gray-400 hover:text-red-500 mb-2">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Photo Gallery Repeater -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Image Gallery</h4>
                            <p class="text-xs text-gray-400">Add multiple gallery images representing standard rooms,
                                dining areas, or workspaces.</p>
                        </div>
                        <button type="button" @click="addGalleryImage"
                            class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                            <span class="material-symbols-outlined text-sm">add_photo_alternate</span> Add Photo
                        </button>
                    </div>

                    <div v-if="form.gallery.length === 0"
                        class="flex h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 text-gray-400 dark:border-gray-800">
                        <span class="material-symbols-outlined mb-1 text-2xl">collections</span>
                        <span class="text-xs font-medium">No gallery images added yet</span>
                    </div>

                    <ul v-else class="space-y-6">
                        <li v-for="(img, index) in form.gallery" :key="index"
                            class="flex flex-wrap gap-5 items-end border-b border-gray-100 pb-5 last:border-0 last:pb-0 dark:border-gray-800/50">
                            <!-- Preserves the database primary key during updates -->
                            <input v-if="img.id" type="hidden" :value="img.id" />

                            <div class="w-80 min-w-[240px]">
                                <InputLabel :for="`gal_file_${index}`" value="Upload Gallery Image" />
                                <FileUploader :id="`gal_file_${index}`" v-model="img.media_id"
                                    :initial-file-url="img.image_url" class="mt-1" />
                            </div>

                            <div class="flex-1 min-w-[240px]">
                                <InputLabel :for="`gal_caption_${index}`" value="Photo Caption" />
                                <TextInput :id="`gal_caption_${index}`" v-model="img.caption"
                                    placeholder="e.g. Master suite balcony sunset view" class="mt-1 block w-full" />
                            </div>

                            <div class="w-20">
                                <InputLabel :for="`gal_sort_${index}`" value="Sort" />
                                <TextInput :id="`gal_sort_${index}`" v-model="img.sort_order" type="number"
                                    class="mt-1 block w-full text-center" />
                            </div>

                            <div class="mb-2 flex items-center gap-2">
                                <input type="checkbox" :id="`gal_cover_${index}`" v-model="img.is_cover"
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-800" />
                                <InputLabel :for="`gal_cover_${index}`" value="Set as Cover"
                                    class="cursor-pointer text-xs" />
                            </div>

                            <div class="pb-2">
                                <button type="button" @click="removeGalleryImage(index)"
                                    class="text-gray-400 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Downloadable Resources Documents -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Documents & Fact Sheets
                            </h4>
                            <p class="text-xs text-gray-400">Attach brochures, floor plans, and technical specification
                                files (PDFs up to 10MB).</p>
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
                            <!-- Preserves the database primary key during update reconciliations -->
                            <input v-if="doc.id" type="hidden" :value="doc.id" />

                            <div class="flex-1 min-w-[240px]">
                                <InputLabel :for="`doc_label_${index}`" value="Document Label" />
                                <TextInput :id="`doc_label_${index}`" v-model="doc.label"
                                    placeholder="e.g. Sana Beach Lodge Fact Sheet" class="mt-1 block w-full" required />
                            </div>

                            <div class="w-80 min-w-[240px]">
                                <InputLabel :for="`doc_file_${index}`" value="Replace File Attachment" />
                                <FileUploader :id="`doc_file_${index}`" v-model="doc.file_id"
                                    :initial-file-url="doc.file_url" class="mt-1" />
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
                    <Link :href="route('app.admin.properties.index')"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950">
                        Cancel
                    </Link>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Changes</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
