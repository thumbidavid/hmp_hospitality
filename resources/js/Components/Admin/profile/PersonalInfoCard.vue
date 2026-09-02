<!-- PersonalInfoCard.vue -->
<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
// FIX: Import the modal component needed
import Modal from '@/Components/Modal.vue';
// FIX: We need the actual form logic component for the modal
import UpdateProfileInformationForm from './UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

// Get current user data
const user = computed(() => usePage().props.auth.user);

// Local state for the modal
const isProfileInfoModal = ref(false);
</script>

<template>
    <div>
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <!-- Card Title: Sourcing the text directly from the template -->
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
                        Personal Information
                    </h4>

                    <!-- Static Data Display (Sourced from template classes and live user data) -->
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">

                        <!-- First Name -->
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">First Name</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user.name.split(' ')[0] || user.name }}</p>
                        </div>

                        <!-- Last Name (Assumption: using the rest of the name for last name) -->
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Last Name</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user.name.split(' ').slice(1).join(' ') }}</p>
                        </div>

                        <!-- Email address -->
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                Email address
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ user.email }}
                            </p>
                        </div>

                        <!-- Phone (Placeholder/Assumption) -->
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user.phone || '+09 363 398 46' }}</p>
                        </div>

                        <!-- Bio (Placeholder/Assumption) -->
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Bio</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user.bio || 'Team Manager' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Button (Sourced from template classes) -->
                <button class="edit-button" @click="isProfileInfoModal = true">
                    <svg
                        class="fill-current"
                        width="18"
                        height="18"
                        viewBox="0 0 18 18"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                            fill=""
                        />
                    </svg>
                    Edit
                </button>
            </div>
        </div>

        <!-- Modal (Sourced from template classes) -->
        <Modal v-if="isProfileInfoModal" @close="isProfileInfoModal = false">
            <template #body>
                <div
                    class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
                >
                    <!-- Close Button -->
                    <button
                        @click="isProfileInfoModal = false"
                        class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300"
                    >
                        <svg
                            class="fill-current"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                                fill=""
                            />
                        </svg>
                    </button>

                    <!-- Modal Header -->
                    <div class="px-2 pr-14">
                        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Edit Personal Information
                        </h4>
                        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                            Update your details to keep your profile up-to-date.
                        </p>
                    </div>

                    <!-- Form Component (Pass required props) -->
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        @success="isProfileInfoModal = false"
                        class="px-2"
                    />

                    <!-- FIX: Implement the modal footer logic from the template (Close/Save buttons)
                         NOTE: The Save button is usually INCLUDED in the form component, so we'll
                         only keep the Close button logic outside if needed for the layout.
                         Let's assume the form has the Save button.
                    -->

                </div>
            </template>
        </Modal>
    </div>
</template>
