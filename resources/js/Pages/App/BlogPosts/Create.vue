<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
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
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue'

const props = defineProps({
    categories: Array
})

// Setup form data
const form = useForm({
    blog_category_id: '',
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: null, // Receives asynchronously uploaded media ID
    read_minutes: '',
    is_featured: false,
    is_active: true,
    published_at: ''
})

const submit = () => {
    form.post(route('app.admin.blog-posts.store'), {
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Write Article" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Draft New Story" />

        <div class="mx-auto max-w-full pb-16">
            <!-- Back navigation panel -->
            <div class="mb-6">
                <Link :href="route('app.admin.blog-posts.index')"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to Articles
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                    <!-- Left Columns: Title and Editor -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Article Content
                            </h4>

                            <div class="space-y-5">
                                <div>
                                    <InputLabel for="title" value="Article Title" />
                                    <TextInput id="title" v-model="form.title" type="text"
                                        placeholder="e.g. Preserving Heritage: Nairobi's Architectural Milestones"
                                        class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <InputLabel for="slug" value="URL Slug (Optional)" />
                                        <TextInput id="slug" v-model="form.slug" type="text"
                                            placeholder="e.g. preserving-heritage-nairobi" class="mt-1 block w-full" />
                                        <InputError class="mt-2" :message="form.errors.slug" />
                                    </div>

                                    <div>
                                        <InputLabel for="blog_category_id" value="Article Category" />
                                        <SelectInput id="blog_category_id" v-model="form.blog_category_id"
                                            class="mt-1 block w-full" required>
                                            <option value="" disabled>Select Category</option>
                                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name
                                            }}</option>
                                        </SelectInput>
                                        <InputError class="mt-2" :message="form.errors.blog_category_id" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="excerpt" value="Excerpt / Short Summary" />
                                    <TextArea id="excerpt" v-model="form.excerpt"
                                        placeholder="Provide a brief summary snippet for card grids and SEO..."
                                        class="mt-1 block w-full" rows="3" />
                                    <InputError class="mt-2" :message="form.errors.excerpt" />
                                </div>

                                <div>
                                    <InputLabel for="content" value="Full Article Body" />
                                    <RichTextEditor id="content" v-model="form.content" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.content" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Media, Status, Scheduling -->
                    <div class="space-y-6">
                        <!-- Cover Image Upload Box -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Header Cover Image
                            </h4>
                            <FileUploader id="featured_image" v-model="form.featured_image" />
                            <InputError class="mt-2" :message="form.errors.featured_image" />
                        </div>

                        <!-- Visibility Options & Metadata -->
                        <div
                            class="rounded-3xl border border-gray-100 bg-white p-6 dark:border-gray-800/50 dark:bg-gray-900">
                            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Publishing Options
                            </h4>

                            <div class="space-y-5">
                                <div>
                                    <InputLabel for="published_at" value="Publishing Date" />
                                    <TextInput id="published_at" v-model="form.published_at" type="date"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.published_at" />
                                </div>

                                <div>
                                    <InputLabel for="read_minutes" value="Estimated Read Time (Minutes)" />
                                    <TextInput id="read_minutes" v-model="form.read_minutes" type="number"
                                        placeholder="e.g. 4" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.read_minutes" />
                                </div>

                                <div class="flex flex-col gap-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                                    <Checkbox id="is_active" v-model="form.is_active" label="Publish Instantly">
                                        Render this article visible in collection feeds.
                                    </Checkbox>

                                    <Checkbox id="is_featured" v-model="form.is_featured" label="Featured Story">
                                        Feature this article on the main collection landing page header.
                                    </Checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Operations Controls -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <Link :href="route('app.admin.blog-posts.index')"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950">
                        Cancel
                    </Link>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Publishing...</span>
                        <span v-else>Save Article</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
