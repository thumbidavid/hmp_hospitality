<script setup>
import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('admin.logout'))
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="flex w-64 flex-col bg-slate-900 text-white">
            <div class="border-b border-slate-800 p-6 text-xl font-bold">Admin Panel</div>
            <nav class="flex-1 space-y-2 p-4">
                <Link
                    :href="route('admin.dashboard')"
                    class="block rounded p-3 hover:bg-slate-800"
                    :class="{ 'bg-blue-600': $page.component === 'Admin/Dashboard' }"
                    >Dashboard</Link
                >
                <Link
                    :href="route('admin.profile.edit')"
                    class="block rounded p-3 hover:bg-slate-800"
                    >Settings</Link
                >
            </nav>
            <div class="border-t border-slate-800 p-4">
                <button
                    @click="logout"
                    class="w-full rounded p-3 text-left text-red-400 hover:bg-slate-800"
                >
                    Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <header class="flex h-16 items-center justify-end bg-white px-8 shadow">
                <span class="text-gray-600">Welcome, {{ $page.props.auth.user.name }}</span>
            </header>
            <main class="p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
