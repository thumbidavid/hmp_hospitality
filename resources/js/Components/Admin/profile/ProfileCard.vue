<!-- ProfileCard.vue -->
<script setup>
import { computed, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { ChevronDownIcon } from '@/icons';
import Modal from '@/Components/Modal.vue'; // Assuming Modal is available

const user = computed(() => usePage().props.auth.user);

// Placeholder for the social buttons' edit modal (ProfileCard template includes an Edit button)
const isProfileInfoModal = ref(false);

// Note: You need to define the 'social-button' and 'edit-button' classes in your global CSS or Tailwind config
// to fully match the template's look. For now, we ensure the container is correct.
</script>

<template>
  <div>
    <!-- FIX 1: Outer Border/Padding/Margin (Looks Correct) -->
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <!-- Profile Image Border (Looks Correct) -->
          <div
            class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800"
          >
            <img :src="user.profile_photo_url || '/images/user/owner.jpg'" :alt="user.name" class="object-cover w-full h-full" />
          </div>

          <!-- User Info -->
          <div class="order-3 xl:order-2">
            <!-- User Name Text (Looks Correct) -->
            <h4
              class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left"
            >
              {{ user.name }}
            </h4>
            <div
              class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left"
            >
              <!-- Role/Location Text (Looks Correct) -->
              <p class="text-sm text-gray-500 dark:text-gray-400">Team Manager</p>

              <!-- Divider FIX 2: Dark Mode Border for Divider -->
              <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>

              <p class="text-sm text-gray-500 dark:text-gray-400">Arizona, United States</p>
            </div>
          </div>

          <!-- Social Links (Container looks correct) -->
          <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
            <!-- Social Buttons (Assuming 'social-button' handles its own dark mode styles) -->
            <a href="#" class="social-button">Facebook</a>
            <a href="#" class="social-button">X.com</a>
            <a href="#" class="social-button">LinkedIn</a>
            <a href="#" class="social-button">Instagram</a>
          </div>
        </div>

        <!-- Edit Button (Assuming 'edit-button' handles its own dark mode styles) -->
        <button @click="isProfileInfoModal = true" class="edit-button">
          <!-- SVG and text from your ProfileCard template -->
          <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill=""/>
          </svg>
          Edit
        </button>
      </div>
    </div>

    <!-- Modal for Social Links/Profile Picture Edit -->
    <Modal v-if="isProfileInfoModal" @close="isProfileInfoModal = false">
        <template #body>
            <!-- FIX 3: Modal body wrapper (Applies background/text/padding for dark mode) -->
            <div
                class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 dark:text-gray-100"
            >
              <p>Modal content (Social Links/Picture Edit)</p>
            </div>
        </template>
    </Modal>
  </div>
</template>
