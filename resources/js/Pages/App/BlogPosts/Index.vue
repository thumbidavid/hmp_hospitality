<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    posts: Array
})

// Initialize useResourceCRUD for deletes and standard event tracking
const crud = useResourceCRUD({
    resourceName: 'Blog Post',
    entityName: 'blog_post',
    routeNames: {
        store: 'app.admin.blog-posts.store',
        update: 'app.admin.blog-posts.update',
        destroy: 'app.admin.blog-posts.destroy',
    },
    initialFormState: () => ({}),
    fieldsConfig: [],
})

const createPost = () => {
    window.location.href = route('app.admin.blog-posts.create')
}

const editPost = (item) => {
    window.location.href = route('app.admin.blog-posts.edit', item.id)
}

const columns = ref([
    { key: 'featured_image_url', label: 'Image' },
    { key: 'title', label: 'Article Title' },
    { key: 'category', label: 'Category' },
    { key: 'author', label: 'Author' },
    { key: 'read_minutes', label: 'Read Time' },
    { key: 'is_featured', label: 'Featured' },
    { key: 'is_active', label: 'Status' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Blog Article Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Stories from Our Collection" />

        <DataTable :data="posts" :columns="columns" @edit-item="editPost" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="createPost">Draft New Article</PrimaryButton>
            </template>

            <!-- Header Cover Image Slot -->
            <template #cell-featured_image_url="{ item }">
                <div
                    class="h-12 w-16 overflow-hidden rounded-lg bg-gray-100 border border-gray-200 dark:border-gray-700">
                    <img v-if="item.featured_image_url" :src="item.featured_image_url"
                        class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-xl">image</span>
                    </div>
                </div>
            </template>

            <!-- Custom Category Display -->
            <template #cell-category="{ item }">
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                    {{ item.blog_category ? item.blog_category.name : 'Uncategorized' }}
                </span>
            </template>

            <!-- Custom Author Display -->
            <template #cell-author="{ item }">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ item.author ? item.author.name : 'System Writer' }}
                </span>
            </template>

            <!-- Estimated Read Time -->
            <template #cell-read_minutes="{ item }">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ item.read_minutes ? `${item.read_minutes} min read` : 'N/A' }}
                </span>
            </template>

            <!-- Featured Article Status -->
            <template #cell-is_featured="{ item }">
                <span v-if="item.is_featured"
                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                    Featured
                </span>
                <span v-else class="text-xs text-gray-400">Standard</span>
            </template>

            <!-- Visibility Status -->
            <template #cell-is_active="{ item }">
                <span :class="item.is_active
                    ? 'text-green-600 dark:text-green-400 font-semibold text-xs'
                    : 'text-gray-400 font-semibold text-xs'">
                    {{ item.is_active ? 'Published' : 'Draft' }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
