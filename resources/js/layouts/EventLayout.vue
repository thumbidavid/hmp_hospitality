<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    event: { type: Object, required: true },
})

const tabs = [
    { name: 'Overview', route: 'corporate.events.overview' },
    { name: 'RFPs', route: 'corporate.events.rfps' },
    { name: 'Sourcing', route: 'corporate.events.sourcing' },
    { name: 'Proposals', route: 'corporate.events.proposals' },
    { name: 'Tasks', route: 'corporate.events.tasks' },
    { name: 'Budget', route: 'corporate.events.budget' },
    { name: 'Attendees', route: 'corporate.events.attendees' },
    { name: 'Schedule', route: 'corporate.events.schedule' },
    { name: 'Documents', route: 'corporate.events.documents' },
    { name: 'Communications', route: 'corporate.events.communications' },
    { name: 'Activity', route: 'corporate.events.activity' },
]
</script>

<template>
    <AuthenticatedLayout>
        <div v-if="event.id" class="px-8 pt-0">
            <!-- New Header Layout -->
            <div class="mb-2 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white/90">
                        {{ event.name }}
                    </h3>
                </div>

                <!-- Icon/Menu Button -->
                <div class="relative inline-block">
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex gap-6 overflow-x-auto border-b border-gray-100 dark:border-gray-800">
                <Link v-for="tab in tabs" :key="tab.name" :href="route(tab.route, event.id)" :class="[
                    route().current(tab.route)
                        ? 'border-blue-600 text-blue-600 dark:text-blue-500'
                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400',
                ]" class="border-b-2 py-3 text-sm font-medium whitespace-nowrap transition-all">
                    {{ tab.name }}
                </Link>
            </div>
        </div>

        <div v-else class="p-8">Loading event...</div>

        <!-- Page Content -->
        <div class="p-8">
            <slot />
        </div>
    </AuthenticatedLayout>
</template>
