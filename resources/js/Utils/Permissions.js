import { usePage } from '@inertiajs/vue3'

export function useRole() {
  const user = usePage().props.auth.user

  const hasRole = (role) => user?.roles.includes(role) ?? false

  const hasAnyRole = (roles) => roles.some((role) => user?.roles.includes(role)) ?? false

  return { hasRole, hasAnyRole }
}
