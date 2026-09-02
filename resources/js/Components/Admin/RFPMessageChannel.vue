<script setup>
import { ref, onMounted, watch, nextTick, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

// --- PROPS ---
const props = defineProps({
    rfp: { type: Object, required: true },
    // A list of potential people to talk to (e.g., the client and all targeted vendors)
    participants: { type: Array, required: true },
    suite: {
        type: String,
        required: true,
        validator: (value) => ["client", "vendor"].includes(value),
    },
});

const currentUser = usePage().props.auth.user;
const messages = ref([]);
const newMessageText = ref("");
const chatContainer = ref(null);
const isLoading = ref(true);

// The person the current user is talking to.
const selectedParticipant = ref(null);

// --- COMPUTED PROPERTIES ---
// The full conversation with the selected participant.
const currentMessages = computed(() => {
    if (!selectedParticipant.value) return [];
    return messages.value.filter(
        (msg) =>
            (msg.sender_user_id === currentUser.id &&
                msg.recipient_user_id === selectedParticipant.value.id) ||
            (msg.sender_user_id === selectedParticipant.value.id &&
                msg.recipient_user_id === currentUser.id)
    );
});

const otherParticipants = computed(() => {
    return props.participants.filter((p) => p.id !== currentUser.id);
});

// --- METHODS ---
const scrollToBottom = () =>
    nextTick(() => {
        if (chatContainer.value)
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    });

const fetchMessages = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(
            route("client.rfps.messages.index", props.rfp.id)
        ); // Route name is the same for both suites
        messages.value = response.data;
    } catch (error) {
        console.error("Failed to fetch messages:", error);
    } finally {
        isLoading.value = false;
        scrollToBottom();
    }
};

const sendMessage = async () => {
    if (newMessageText.value.trim() === "" || !selectedParticipant.value)
        return;

    const payload = {
        message: newMessageText.value,
        recipient_user_id: selectedParticipant.value.id,
    };

    const tempMessage = {
        id: Date.now(),
        message: newMessageText.value,
        sender: currentUser,
        sender_user_id: currentUser.id,
        created_at: new Date().toISOString(),
    };
    messages.value.push(tempMessage);
    newMessageText.value = "";

    try {
        const response = await axios.post(
            route("client.rfps.messages.store", props.rfp.id),
            payload
        );
        // Replace temp message with real one from server
        const index = messages.value.findIndex((m) => m.id === tempMessage.id);
        if (index !== -1) {
            messages.value.splice(index, 1, response.data);
        }
    } catch (error) {
        console.error("Failed to send message:", error);
        // Optionally, add failure UI to the temp message
    }
};

// --- LIFECYCLE HOOKS ---
onMounted(() => {
    // Automatically select the first participant to talk to (e.g., the client if you're a vendor)
    if (props.participants.length > 0) {
        selectedParticipant.value = props.participants[0];
    }
    fetchMessages();
});

watch(selectedParticipant, () => {
    scrollToBottom();
});
</script>

<template>
    <!-- This is your exact chat template, wired to live data -->
    <div class="h-[calc(100vh-300px)] overflow-hidden">
        <div class="flex h-full flex-col gap-6 xl:flex-row xl:gap-5">
            <!-- Chat Sidebar (List of Participants) -->
            <div
                class="w-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 xl:w-1/4 flex"
            >
                <div
                    class="custom-scrollbar flex-col overflow-y-auto w-full h-full"
                >
                    <div class="px-4 pt-4 pb-4 sm:px-5 sm:pt-5">
                        <h3
                            class="text-xl font-semibold text-gray-800 dark:text-white sm:text-2xl"
                        >
                            Conversations
                        </h3>
                    </div>
                    <div class="flex-col px-4 sm:px-5">
                        <div class="mb-4">
                            <h4
                                class="px-3 text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Participants
                            </h4>
                            <div class="mt-2 space-y-1">
                                <!-- Loop over participants -->
                                <div
                                    v-for="person in otherParticipants"
                                    :key="person.id"
                                    @click="selectedParticipant = person"
                                    class="flex cursor-pointer items-center gap-3 rounded-lg p-3"
                                    :class="
                                        selectedParticipant?.id === person.id
                                            ? 'bg-gray-100 dark:bg-gray-800'
                                            : 'hover:bg-gray-100 dark:hover:bg-gray-800'
                                    "
                                >
                                    <div
                                        class="relative h-12 w-12 flex-shrink-0 rounded-full"
                                    >
                                        <img
                                            :src="`https://ui-avatars.com/api/?name=${person.name.replace(
                                                /\s/g,
                                                '+'
                                            )}&background=random`"
                                            alt="profile"
                                            class="h-full w-full rounded-full object-cover"
                                        />
                                    </div>
                                    <div class="w-full">
                                        <h5
                                            class="text-sm font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ person.name }}
                                        </h5>
                                        <p
                                            class="text-xs mt-0.5 text-gray-500 dark:text-gray-400"
                                        >
                                            {{ person.organization.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Box -->
            <div
                v-if="selectedParticipant"
                class="flex h-full w-full flex-1 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 xl:w-3/4"
            >
                <div
                    class="sticky flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800 xl:px-6"
                >
                    <div class="flex items-center gap-3">
                        <div class="relative h-12 w-12 rounded-full">
                            <img
                                :src="`https://ui-avatars.com/api/?name=${selectedParticipant.name.replace(
                                    /\s/g,
                                    '+'
                                )}&background=random`"
                                alt="profile"
                                class="h-full w-full rounded-full object-cover"
                            />
                        </div>
                        <div>
                            <h5
                                class="text-sm font-medium text-gray-800 dark:text-white"
                            >
                                {{ selectedParticipant.name }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div
                    ref="chatContainer"
                    class="custom-scrollbar max-h-full flex-1 space-y-6 overflow-auto p-5 xl:space-y-8 xl:p-6"
                >
                    <!-- Loop over messages -->
                    <div v-for="message in currentMessages" :key="message.id">
                        <!-- Other person's message -->
                        <div
                            v-if="message.sender_user_id !== currentUser.id"
                            class="max-w-sm"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    class="h-10 w-10 flex-shrink-0 rounded-full"
                                >
                                    <img
                                        :src="`https://ui-avatars.com/api/?name=${message.sender.name.replace(
                                            /\s/g,
                                            '+'
                                        )}&background=random`"
                                        alt="profile"
                                        class="h-full w-full rounded-full object-cover"
                                    />
                                </div>
                                <div>
                                    <div
                                        class="inline-block rounded-lg rounded-tl-none bg-gray-100 px-3 py-2 dark:bg-gray-800"
                                    >
                                        <p
                                            class="text-sm text-gray-800 dark:text-white"
                                        >
                                            {{ message.message }}
                                        </p>
                                    </div>
                                    <p
                                        class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ message.sender.name }},
                                        {{
                                            new Date(
                                                message.created_at
                                            ).toLocaleTimeString()
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Your message -->
                        <div v-else class="ml-auto max-w-sm text-right">
                            <div
                                class="ml-auto inline-block rounded-lg rounded-br-none bg-indigo-500 px-3 py-2"
                            >
                                <p class="text-sm text-white">
                                    {{ message.message }}
                                </p>
                            </div>
                            <p
                                class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{
                                    new Date(
                                        message.created_at
                                    ).toLocaleTimeString()
                                }}
                            </p>
                        </div>
                    </div>
                    <div v-if="isLoading" class="text-center text-gray-500">
                        Loading messages...
                    </div>
                </div>
                <div
                    class="sticky bottom-0 border-t border-gray-200 p-3 dark:border-gray-800"
                >
                    <form
                        @submit.prevent="sendMessage"
                        class="flex items-center justify-between"
                    >
                        <div class="relative w-full">
                            <input
                                v-model="newMessageText"
                                type="text"
                                placeholder="Type a message"
                                class="h-9 w-full border-none bg-transparent pl-5 pr-5 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:ring-0 dark:text-white"
                            />
                        </div>
                        <div class="flex items-center">
                            <button
                                type="submit"
                                class="ml-3 flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 xl:ml-5"
                            >
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M4.98481 2.44399C3.11333 1.57147 1.15325 3.46979 1.96543 5.36824L3.82086 9.70527C3.90146 9.89367 3.90146 10.1069 3.82086 10.2953L1.96543 14.6323C1.15326 16.5307 3.11332 18.4291 4.98481 17.5565L16.8184 12.0395C18.5508 11.2319 18.5508 8.76865 16.8184 7.961L4.98481 2.44399ZM3.34453 4.77824C3.0738 4.14543 3.72716 3.51266 4.35099 3.80349L16.1846 9.32051C16.762 9.58973 16.762 10.4108 16.1846 10.68L4.35098 16.197C3.72716 16.4879 3.0738 15.8551 3.34453 15.2223L5.19996 10.8853C5.21944 10.8397 5.23735 10.7937 5.2537 10.7473L9.11784 10.7473C9.53206 10.7473 9.86784 10.4115 9.86784 9.99726C9.86784 9.58304 9.53206 9.24726 9.11784 9.24726L5.25157 9.24726C5.2358 9.20287 5.2186 9.15885 5.19996 9.11528L3.34453 4.77824Z"
                                        fill="white"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div
                v-else
                class="hidden h-full flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white text-gray-500 dark:border-gray-800 dark:bg-gray-900 xl:flex xl:w-3/4"
            >
                <p>Select a conversation to start messaging</p>
            </div>
        </div>
    </div>
</template>
