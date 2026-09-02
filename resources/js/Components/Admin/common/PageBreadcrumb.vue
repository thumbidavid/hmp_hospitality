<template>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ pageTitle }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <!-- DYNAMIC HOME LINK: Uses context-aware homeRoute -->
                    <Link
                        class="hover:text-brand-500 dark:hover:text-brand-400 inline-flex items-center gap-1.5 text-sm text-gray-500 transition-colors dark:text-gray-400"
                        :href="route(homeRoute)"
                    >
                        Home
                        <svg
                            class="stroke-current"
                            width="17"
                            height="16"
                            viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                stroke-width="1.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </Link>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">
                    {{ pageTitle }}
                </li>
            </ol>
        </nav>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

// 1. Define Props
const props = defineProps({
    pageTitle: {
        type: String,
        required: true,
    },
})

// 2. Access Auth Context
const page = usePage()
const org = computed(() => page.props.auth.organization)
const role = computed(() => page.props.auth.role)

/**
 * 3. CONTEXTUAL HOME ROUTE
 * Resolves the breadcrumb 'Home' destination based on org type and role.
 */
const homeRoute = computed(() => {
    if (!org.value) return 'home'

    // Meeta Internal Admin
    if (role.value === 'system_admin') {
        return 'platform.dashboard'
    }

    // Corporate Buyer
    if (org.value.type === 'corporate') {
        return 'corporate.dashboard'
    }

    // Supplier Seller
    if (org.value.type === 'supplier') {
        return 'supplier.dashboard'
    }

    return 'home'
})
</script>
