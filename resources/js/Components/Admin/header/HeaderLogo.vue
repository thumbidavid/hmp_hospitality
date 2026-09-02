<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const org = computed(() => page.props.auth.organization)
const role = computed(() => page.props.auth.role)

/**
 * Determine the dynamic route based on User Role and Org Type.
 * Matches the logic in the Sidebar for a unified UX.
 */
const logoRoute = computed(() => {
    if (!org.value) return 'home'

    // System Admins (Platform Level)
    if (role.value === 'system_admin') {
        return 'platform.organizations.index'
    }

    // Corporate Users (Buyer Level)
    if (org.value.type === 'corporate') {
        return 'corporate.dashboard'
    }

    // Supplier Users (Seller Level)
    if (org.value.type === 'supplier') {
        return 'supplier.dashboard'
    }

    return 'home'
})
</script>

<template>
    <!-- Use the computed logoRoute to ensure the user returns to their specific context -->
    <Link :href="route(logoRoute)" class="lg:hidden">
        <img class="dark:hidden" src="/logo-dark.png" alt="Meeta Logo" />
        <img class="hidden dark:block" src="/logo-dark.png" alt="Meeta Logo" />
    </Link>
</template>
