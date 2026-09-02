<!-- SecurityCard.vue (Password only) -->
<script setup>
import UpdatePasswordForm from './UpdatePasswordForm.vue';
</script>

<template>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
            Account Security
        </h4>
        <UpdatePasswordForm class="max-w-none lg:max-w-none 2xl:max-w-none" />
    </div>
</template>
