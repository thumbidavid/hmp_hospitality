<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminGuestLayout from '@/Layouts/AdminGuestLayout.vue'

defineProps({
    organization: Object,
})

const logoutForm = useForm({})

const logout = () => {
    logoutForm.post(route('logout'))
}
</script>

<template>
    <AdminGuestLayout>
        <Head title="Account Pending Approval" />

        <div
            class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8 dark:bg-gray-950"
        >
            <div
                class="w-full max-w-md space-y-8 rounded-2xl bg-white p-10 shadow-xl dark:bg-gray-900"
            >
                <div class="text-center">
                    <!-- Icon -->
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30"
                    >
                        <svg
                            class="h-10 w-10 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>

                    <h2
                        class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Review in Progress
                    </h2>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        Hello, <span class="font-semibold">{{ $page.props.auth.user.name }}</span
                        >. Your application for
                        <span class="font-bold text-blue-600">{{ organization.name }}</span> is
                        currently being reviewed by our compliance team.
                    </p>
                </div>

                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-blue-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                Status: <strong>Pending Verification</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-center text-xs text-gray-500">
                        We typically verify new organizations within 24 business hours. You will
                        receive an email once your account is activated.
                    </p>

                    <div class="flex justify-center">
                        <button
                            @click="logout"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            Sign Out
                        </button>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Meeta Platform. All rights reserved.
            </p>
        </div>
    </AdminGuestLayout>
</template>
