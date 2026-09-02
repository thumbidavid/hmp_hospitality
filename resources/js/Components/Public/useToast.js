import { ref } from 'vue'

const toasts = ref([])
let count = 0

export function useToast() {
    /**
     * Trigger a new toast notification
     * @param {Object} options - { title, description, variant, duration }
     */
    const toast = ({ title, description, variant = 'default', duration = 5000 }) => {
        const id = count++
        const newToast = { id, title, description, variant }

        toasts.value.push(newToast)

        if (duration > 0) {
            setTimeout(() => {
                dismiss(id)
            }, duration)
        }
    }

    const dismiss = (id) => {
        toasts.value = toasts.value.filter((t) => t.id !== id)
    }

    return {
        toasts,
        toast,
        dismiss,
    }
}
