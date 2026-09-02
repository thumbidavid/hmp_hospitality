import { defineStore } from 'pinia'
import { shallowRef } from 'vue'

export const useModalStore = defineStore('modal', {
    state: () => ({
        isOpen: false,
        component: null,
        props: {},
    }),

    actions: {
        /**
         * Opens a modal.
         * @param {object} payload - The payload object.
         * @param {Component} payload.component - The Vue component to render inside the modal.
         * @param {object} [payload.props={}] - The props to pass to the component.
         */
        open(payload) {
            // Destructure the component and props from the single payload object.
            // This is the critical fix.
            const { component, props = {} } = payload

            this.component = shallowRef(component)
            this.props = props
            this.isOpen = true
        },

        close() {
            this.isOpen = false
            // It's good practice to reset the state completely on close.
            this.component = null
            this.props = {}
        },
    },
})
