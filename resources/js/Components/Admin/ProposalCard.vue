<script setup>
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    proposal: Object,
    isAwarding: Boolean, // Is any proposal currently in the process of being awarded?
    isRfpAwarded: Boolean, // Has the parent RFP already been awarded?
});

const emit = defineEmits(["award"]);

// A proposal is the "winner" if its status is 'Awarded'
const isWinner = computed(() => {
    // Assuming 'Awarded' status ID is 3.
    return props.proposal.status_id === 3;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
};
</script>

<template>
    <!-- Change border color if this is the winning proposal -->
    <div
        class="bg-white border rounded-xl p-6 shadow-sm transition-colors"
        :class="isWinner ? 'border-green-500' : 'border-gray-200'"
    >
        <div class="flex justify-between items-start">
            <div>
                <h4 class="text-lg font-semibold text-gray-800">
                    {{ proposal.submitter_organization.name }}
                </h4>
                <p class="text-sm text-gray-500">
                    Submitted on:
                    {{ new Date(proposal.created_at).toLocaleDateString() }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">
                    {{ formatCurrency(proposal.bid_amount) }}
                </div>
            </div>
        </div>

        <div
            v-if="proposal.proposal_details"
            class="mt-4 pt-4 border-t border-gray-200"
        >
            <h5 class="text-sm font-medium text-gray-600 mb-2">Details:</h5>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">
                {{ proposal.proposal_details }}
            </p>
        </div>

        <div class="mt-6 flex justify-end">
            <!-- STATEFUL FOOTER -->

            <!-- State 1: Show "Awarded" badge if this is the winner -->
            <div
                v-if="isWinner"
                class="bg-green-100 text-green-800 text-sm font-semibold px-4 py-2 rounded-xl"
            >
                Awarded
            </div>

            <!-- State 2: Show "Not Selected" if the RFP is awarded but this isn't the winner -->
            <div
                v-else-if="isRfpAwarded && !isWinner"
                class="bg-gray-100 text-gray-500 text-sm font-semibold px-4 py-2 rounded-xl"
            >
                Not Selected
            </div>

            <!-- State 3: Show the "Award Proposal" button if the RFP is still open -->
            <PrimaryButton
                v-else
                @click="$emit('award', proposal)"
                :disabled="isAwarding"
            >
                <span v-if="isAwarding">Processing...</span>
                <span v-else>Award Proposal</span>
            </PrimaryButton>
        </div>
    </div>
</template>
