<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

// Layout & Specific Admin Components
import AdminGuestLayout from '@/Layouts/AdminGuestLayout.vue'
import CommonGridShape from '@/Components/Admin/common/CommonGridShape.vue'

// UI Components (Updated paths to @/Components/Admin/)
import Checkbox from '@/Components/Admin/Checkbox.vue'
import InputError from '@/Components/Admin/InputError.vue'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import TextInput from '@/Components/Admin/TextInput.vue'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
})

const showPassword = ref(false)
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <AdminGuestLayout>

        <Head title="Log in" />

        <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
            <div class="relative flex h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">
                <!-- Left Column (Form) -->
                <div class="flex w-full flex-1 flex-col lg:w-1/2">
                    <div class="mx-auto w-full max-w-md pt-10">
                        <Link :href="route('home')"
                            class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 20 20" fill="none">
                                <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Back to home
                        </Link>
                    </div>

                    <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                        <!-- Status Message -->
                        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                            {{ status }}
                        </div>

                        <div class="mb-5 sm:mb-8">
                            <h1 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                                Admin Sign In
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Enter your credentials to access the secure area.
                            </p>
                        </div>

                        <div>


                            <!-- Login Form -->
                            <form @submit.prevent="submit">
                                <div class="space-y-5">
                                    <div>
                                        <InputLabel for="email" value="Email" />
                                        <TextInput id="email" type="email" v-model="form.email" required autofocus
                                            autocomplete="username" placeholder="Enter your email" />
                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>

                                    <div>
                                        <InputLabel for="password" value="Password" />
                                        <div class="relative">
                                            <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                                                class="pr-11" v-model="form.password" required
                                                autocomplete="current-password" placeholder="Enter your password" />
                                            <span @click="togglePasswordVisibility"
                                                class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                                <!-- Dynamic Eye Icon -->
                                                <svg v-if="!showPassword" class="fill-current" width="20" height="20"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 13.8c-2.7 0-5.1-1.7-6-4.1.9-2.4 3.3-4.1 6-4.1s5.1 1.7 6 4.1c-.9 2.4-3.3 4.1-6 4.1zm0-9.8C6.5 4 3.5 6.3 2.4 9.5c-.1.1-.1.3 0 .4 1.1 3.2 4.1 5.4 7.6 5.4s6.5-2.2 7.6-5.4c.1-.1.1-.3 0-.4C16.5 6.3 13.5 4 10 4z" />
                                                </svg>
                                                <svg v-else class="fill-current" width="20" height="20"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M4.6 3.6c-.3-.3-.8-.3-1.1 0-.3.3-.3.8 0 1.1l1.3 1.3C3.7 6.8 2.9 8.1 2.4 9.5c-.1.1-.1.3 0 .4 1.1 3.2 4.1 5.4 7.6 5.4 1.3 0 2.4-.3 3.5-.8l1.9 1.9c.3.3.8.3 1.1 0 .3-.3.3-.8 0-1.1L4.6 3.6z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.password" />
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center">
                                            <Checkbox name="remember" v-model:checked="form.remember"
                                                class="text-brand-500 focus:ring-brand-500 h-5 w-5 rounded-md border-gray-300" />
                                            <span class="ms-2 text-sm text-gray-500 dark:text-gray-400">Keep me logged
                                                in</span>
                                        </label>

                                        <Link v-if="canResetPassword" :href="route('password.request')"
                                            class="text-sm font-normal text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            Forgot your password?
                                        </Link>
                                    </div>

                                    <div>
                                        <PrimaryButton type="submit" class="flex w-full items-center justify-center"
                                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Sign In
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-5 text-center sm:text-start">
                                <p class="text-sm font-normal text-gray-700 dark:text-gray-400">
                                    Don't have an account?
                                    <Link :href="route('register')"
                                        class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium">
                                        Sign Up
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Branding) -->
                <div class="relative hidden h-full w-full items-center bg-slate-950 lg:grid lg:w-1/2 dark:bg-white/5">
                    <div class="z-1 flex items-center justify-center">
                        <CommonGridShape />
                        <div class="flex max-w-xs flex-col items-center">
                            <Link :href="route('home')" class="mb-4 block">
                                <img src="/logo-white.png" alt="Logo" class="w-48" />
                            </Link>
                            <p class="text-center text-gray-400 dark:text-white/60">
                                Secure Admin Dashboard Management System
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminGuestLayout>
</template>
