<!-- UpdateProfileInformationForm.vue -->
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

// FIX: Add emit for closing the modal
const emit = defineEmits(['success']);

defineProps({
    mustVerifyEmail: { type: Boolean, },
    status: { type: String, },
    // Class is passed via $attrs
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    // FIX: Add placeholders for First Name, Last Name, Phone, and Bio if your user model supports it
    first_name: user.name.split(' ')[0],
    last_name: user.name.split(' ').slice(1).join(' ') || '',
    phone: user.phone || '',
    bio: user.bio || '',
});

// FIX: Update patch logic to handle the new fields and emit success
const submit = () => {
    form.patch(route('profile.update'), {
        onSuccess: () => emit('success'),
    });
};

</script>

<template>
    <!-- REMOVED: Original <section> and <header> tags -->

    <form @submit.prevent="submit" class="flex flex-col">
        <!-- Scrollable Area -->
        <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">

            <!-- 1. Social Links Section (Hardcoded/Placeholder from template) -->
            <div>
                <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                    Social Links
                </h5>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                    <!-- Example of a Social Input (Use InputLabel/TextInput) -->
                    <div>
                        <InputLabel for="social-facebook" value="Facebook" />
                        <TextInput
                          id="social-facebook"
                          type="text"
                          value="https://www.facebook.com/PimjoHQ"
                        />
                    </div>
                    <!-- ... Add X.com, Linkedin, Instagram inputs here... -->
                </div>
            </div>

            <!-- 2. Personal Information Section (MAPPING EXISTING FORM FIELDS TO NEW STRUCTURE) -->
            <div class="mt-7">
                <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                    Personal Information
                </h5>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">

                    <!-- First Name -->
                    <div class="col-span-2 lg:col-span-1">
                        <InputLabel for="first_name" value="First Name" />
                        <TextInput
                            id="first_name"
                            type="text"
                            v-model="form.first_name"
                            required
                            autocomplete="given-name"
                        />
                        <InputError class="mt-2" :message="form.errors.first_name" />
                    </div>

                    <!-- Last Name -->
                    <div class="col-span-2 lg:col-span-1">
                        <InputLabel for="last_name" value="Last Name" />
                        <TextInput
                            id="last_name"
                            type="text"
                            v-model="form.last_name"
                            autocomplete="family-name"
                        />
                        <InputError class="mt-2" :message="form.errors.last_name" />
                    </div>

                    <!-- Email Address -->
                    <div class="col-span-2 lg:col-span-1">
                        <InputLabel for="email" value="Email Address" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Phone (Placeholder for assumed new field) -->
                    <div class="col-span-2 lg:col-span-1">
                        <InputLabel for="phone" value="Phone" />
                        <TextInput
                            id="phone"
                            type="text"
                            v-model="form.phone"
                            autocomplete="tel"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <!-- Bio (Placeholder for assumed new field) -->
                    <div class="col-span-2">
                        <InputLabel for="bio" value="Bio" />
                        <TextInput
                            id="bio"
                            type="text"
                            v-model="form.bio"
                        />
                        <InputError class="mt-2" :message="form.errors.bio" />
                    </div>
                </div>

                <!-- Email Verification Status (If unverified) -->
                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                        Your email address is unverified.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                    >
                        A new verification link has been sent to your email address.
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer: Save and Close Buttons -->
        <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
            <!-- Close Button (Triggers success event which the card shell uses to close modal) -->
            <button
                @click="$emit('success')"
                type="button"
                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
            >
                Close
            </button>

            <!-- Save Button (Primary) -->
            <PrimaryButton
                type="submit"
                class="flex w-full justify-center sm:w-auto"
                :disabled="form.processing"
            >
                Save Changes
            </PrimaryButton>

            <!-- Success Message -->
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >
                    Saved.
                </p>
            </Transition>
        </div>
    </form>
</template>
