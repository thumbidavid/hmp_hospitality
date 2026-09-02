import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useShortlistStore = defineStore('shortlist', () => {
    // 1. Initialize from localStorage if it exists, otherwise fall back to empty array
    const saved = localStorage.getItem('hmp_shortlist')
    const items = ref(saved ? JSON.parse(saved) : [])

    // 2. Reactively watch the items array and write changes to localStorage in real-time
    watch(
        items,
        (newItems) => {
            localStorage.setItem('hmp_shortlist', JSON.stringify(newItems))
        },
        { deep: true }
    )

    const has = (id) => items.value.some((item) => item.id === id)

    const toggle = (item) => {
        if (has(item.id)) {
            items.value = items.value.filter((i) => i.id !== item.id)
        } else {
            items.value.push(item)
        }
    }

    const clear = () => {
        items.value = []
    }

    return {
        items,
        has,
        toggle,
        clear,
    }
})
