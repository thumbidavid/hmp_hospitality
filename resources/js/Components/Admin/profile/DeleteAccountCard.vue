<!-- DeleteAccountCard.vue -->
<script setup>
// FIX 1: Remove Modal import
import DeleteUserForm from './DeleteUserForm.vue';
import { ref } from 'vue';
// REMOVED: import Modal from '@/Components/Modal.vue';
// REMOVED: const confirmingUserDeletion = ref(false);

// Note: The click handler is now useless as DeleteUserForm doesn't expose a corresponding event
</script>

<template>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
            Delete Account
        </h4>

        <!-- FIX 2: Simply render the form which contains its own button and internal modal logic -->
        <DeleteUserForm
            class="max-w-none lg:max-w-none 2xl:max-w-none"
        />

        <!-- FIX 3: REMOVED: The entire redundant <Modal> block -->
    </div>
</template>
