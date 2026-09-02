<script setup>
import { computed } from "vue"
import { Link, usePage } from "@inertiajs/vue3" // Import usePage
import { ArrowUpRight, Heart, MapPin } from "lucide-vue-next"
import { useShortlistStore } from "@/Stores/shortlistStore"

const props = defineProps({
    it: {
        type: Object,
        required: true
    }
})

const page = usePage()
const shortlistStore = useShortlistStore()

// If this is a destination card, it is considered active only if ALL its nested properties are shortlisted
const active = computed(() => {
    if (props.it.type === 'destination') {
        const destProperties = page.props.items.filter(item =>
            item.type === 'property' && item.destination_id === props.it.db_id
        )
        if (destProperties.length === 0) return false
        return destProperties.every(p => shortlistStore.has(p.id))
    }
    return shortlistStore.has(props.it.id)
})

const handleToggleShortlist = () => {
    if (props.it.type === 'destination') {
        // Find all properties belonging to this destination
        const destProperties = page.props.items.filter(item =>
            item.type === 'property' && item.destination_id === props.it.db_id
        )

        // Check if all of these destination properties are already shortlisted
        const allSelected = destProperties.every(p => shortlistStore.has(p.id))

        if (allSelected) {
            // If all are selected, remove all
            destProperties.forEach(p => {
                if (shortlistStore.has(p.id)) {
                    shortlistStore.toggle(p)
                }
            })
        } else {
            // Otherwise, add all of them
            destProperties.forEach(p => {
                if (!shortlistStore.has(p.id)) {
                    shortlistStore.toggle(p)
                }
            })
        }
    } else {
        // Standard property toggle
        shortlistStore.toggle(props.it)
    }
}
</script>

<template>
    <article class="property-card group flex flex-col h-full">
        <!-- Thumbnail Block -->
        <div class="relative block aspect-[4/3] overflow-hidden rounded-t-2xl">
            <img :src="it.image" :alt="it.name" loading="lazy" class="h-full w-full object-cover img-rise" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/0 to-black/10" />

            <span
                class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium uppercase tracking-wider text-foreground">
                {{ it.category }}
            </span>

            <!-- Dynamic Heart Button (Applies group toggling if Destination) -->
            <button @click.prevent="handleToggleShortlist" :class="[
                'absolute right-4 top-4 grid place-items-center h-9 w-9 rounded-full backdrop-blur transition-all cursor-pointer shadow-sm',
                active ? 'bg-primary text-primary-foreground' : 'bg-white/85 text-foreground hover:bg-white'
            ]" :aria-label="active ? 'Remove from shortlist' : 'Add to shortlist'">
                <Heart :class="['h-4 w-4', active ? 'fill-current' : '']" />
            </button>
        </div>

        <!-- Details Card Block -->
        <div class="flex flex-1 flex-col p-6 h-full">
            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                <MapPin class="h-3.5 w-3.5 text-primary" />
                <span v-if="it.type === 'destination'">{{ it.country }}</span>
                <span v-else>{{ it.city ? it.city + ', ' : '' }}{{ it.country }}</span>
            </div>

            <h3 class="mt-2 font-heading text-xl text-foreground">
                <Link :href="it.type === 'destination' ? `/destinations/${it.id}` : `/portfolio/${it.id}`"
                    class="hover:text-primary transition-colors leading-tight">
                    {{ it.name }}
                </Link>
            </h3>

            <p class="mt-3 text-sm leading-relaxed text-muted-foreground line-clamp-2">
                {{ it.blurb }}
            </p>

            <div class="mt-5 flex items-center gap-4 border-t border-border pt-4 text-[13px] text-foreground/70">
                <template v-if="it.type === 'destination'">
                    <span class="font-medium text-primary">Destination & DMO</span>
                </template>
                <template v-else>
                    <span>{{ it.rooms }} <span class="text-muted-foreground">rooms</span></span>
                    <span class="text-border">|</span>
                    <span>{{ it.capacity }} <span class="text-muted-foreground">pax largest</span></span>
                </template>
            </div>

            <div class="mt-5 flex items-center gap-3 mt-auto">
                <Link :href="it.type === 'destination' ? `/destinations/${it.id}` : `/portfolio/${it.id}`"
                    class="editorial-link flex-1">
                    {{ it.type === 'destination' ? 'Explore Destination' : 'View Property' }}
                    <ArrowUpRight class="h-4 w-4 arr" />
                </Link>
                <!-- Dynamic Button -->
                <button @click="handleToggleShortlist" :class="[
                    'btn-primary !px-4 !py-2 text-xs cursor-pointer transition-colors',
                    active ? 'bg-ink hover:bg-primary border-ink' : ''
                ]">
                    {{ active ? 'In Shortlist' : 'Add to Shortlist' }}
                </button>
            </div>
        </div>
    </article>
</template>