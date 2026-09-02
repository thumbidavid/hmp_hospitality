<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div
        class="flex min-h-screen flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8 dark:bg-gray-950"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="mb-6 flex justify-center">
                <img src="/images/admin/logo/logo.png" class="h-12 w-auto" alt="Meeta Logo" />
            </div>
        </div>

        <div class="mt-4 px-4 sm:mx-auto sm:w-full sm:max-w-xl">
            <slot />

            <div class="mt-8 text-center">
                <p class="text-xs text-gray-400">
                    Powered by Meeta Platform. Secure Event Management.
                </p>
            </div>
        </div>
    </div>
</template>
