import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
    const page = usePage()
    const permissions = computed(() => page.props.auth.permissions ?? [])
    const role = computed(() => page.props.auth.role)

    const can = (permission) => permissions.value.includes(permission)
    const hasRole = (r) => role.value === r

    return { can, hasRole }
}
