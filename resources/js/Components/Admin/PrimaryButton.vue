<template>
    <!-- Note: We are replacing the default classes but keeping the slot for content -->
    <button
        class="shadow-theme-xs hover:bg-brand-600 flex items-center justify-center rounded-lg bg-[#173f35] px-4 py-3 text-sm font-medium text-white transition">
        <slot />
    </button>
</template>
