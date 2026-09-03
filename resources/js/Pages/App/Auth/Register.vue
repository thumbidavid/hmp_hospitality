<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminGuestLayout from '@/Layouts/AdminGuestLayout.vue'
import CommonGridShape from '@/Components/Admin/common/CommonGridShape.vue'
import InputError from '@/Components/Admin/InputError.vue'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import TextInput from '@/Components/Admin/TextInput.vue'

const form = useForm({
    first_name: '',
    last_name: '',
    company: '',
    job_title: '', // Added as it's useful for Organisers
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <AdminGuestLayout>

        <Head title="Sign Up as Organiser" />

        <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
            <div class="relative flex h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">

                <!-- Left Column (Form) -->
                <div class="flex w-full flex-1 flex-col overflow-y-auto lg:w-1/2">
                    <div class="mx-auto w-full max-w-md pt-10">
                        <Link :href="route('login')"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                            ← Back to Sign In
                        </Link>
                    </div>

                    <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-10">
                        <div class="mb-8">
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Become an Organiser</h1>
                            <p class="text-sm text-gray-500">Register your account to start hosting events on
                                EventPress.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <!-- Name Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="first_name" value="First Name" />
                                    <TextInput id="first_name" v-model="form.first_name" required />
                                    <InputError :message="form.errors.first_name" />
                                </div>
                                <div>
                                    <InputLabel for="last_name" value="Last Name" />
                                    <TextInput id="last_name" v-model="form.last_name" required />
                                    <InputError :message="form.errors.last_name" />
                                </div>
                            </div>

                            <!-- Company Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="company" value="Company" />
                                    <TextInput id="company" v-model="form.company" required />
                                    <InputError :message="form.errors.company" />
                                </div>
                                <div>
                                    <InputLabel for="job_title" value="Job Title" />
                                    <TextInput id="job_title" v-model="form.job_title" required />
                                    <InputError :message="form.errors.job_title" />
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email Address" />
                                <TextInput id="email" type="email" v-model="form.email" required />
                                <InputError :message="form.errors.email" />
                            </div>

                            <!-- Password -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="password" value="Password" />
                                    <TextInput id="password" type="password" v-model="form.password" required />
                                    <InputError :message="form.errors.password" />
                                </div>
                                <div>
                                    <InputLabel for="password_confirmation" value="Confirm" />
                                    <TextInput id="password_confirmation" type="password"
                                        v-model="form.password_confirmation" required />
                                </div>
                            </div>

                            <PrimaryButton type="submit" class="w-full justify-center mt-4" :disabled="form.processing">
                                {{ form.processing ? 'Registering...' : 'Register as Organiser' }}
                            </PrimaryButton>
                        </form>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="relative hidden h-full w-full items-center bg-slate-950 lg:grid lg:w-1/2 dark:bg-white/5">
                    <div class="z-1 flex items-center justify-center">
                        <CommonGridShape />
                        <div class="flex max-w-xs flex-col items-center">
                            <Link :href="route('home')" class="mb-4 block">
                                <img src="/logo_white.png" alt="Logo" class="w-48" />
                            </Link>
                            <p class="text-center text-gray-400 dark:text-white/60">
                                Powering professional events with EventPress.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminGuestLayout>
</template>
