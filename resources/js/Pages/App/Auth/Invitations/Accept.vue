<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import AdminGuestLayout from '@/Layouts/AdminGuestLayout.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import InputError from '@/Components/Admin/InputError.vue'
import { first } from 'lodash'

const props = defineProps({
    token: String,
    email: String,
    organizationName: String,
})

const form = useForm({
    first_name: '',
    last_name: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('invitations.accept', { token: props.token }))
}
</script>

<template>
    <AdminGuestLayout>
        <Head title="Accept Invitation" />

        <div class="flex h-screen items-center justify-center p-6">
            <div
                class="w-full max-w-md rounded-xl border border-gray-100 bg-white p-8 shadow-lg dark:bg-gray-900"
            >
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold">Welcome to {{ organizationName }}</h2>
                    <p class="mt-2 text-sm text-gray-500">You've been invited to join the team.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Read-only Email -->
                    <div>
                        <InputLabel value="Email Address" />
                        <TextInput :value="email" disabled class="cursor-not-allowed" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="First Name" />
                            <TextInput v-model="form.first_name" required />
                            <InputError :message="form.errors.first_name" />
                        </div>
                        <div>
                            <InputLabel value="Last Name" />
                            <TextInput v-model="form.last_name" required />
                            <InputError :message="form.errors.last_name" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            placeholder="••••••••••••"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            placeholder="••••••••••••"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <PrimaryButton :disabled="form.processing" class="w-full justify-center">
                        Accept Invitation & Join
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </AdminGuestLayout>
</template>
