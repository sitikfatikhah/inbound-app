<script setup lang="ts">
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import Input from '@/components/ui/input/Input.vue'

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

import {
    MoreHorizontal,
    ShieldCheck,
    Users,
    KeyRound,
    Plus,
    Search,
} from 'lucide-vue-next'

interface Permission {
    id: number
    name: string
}

interface Role {
    id: number
    name: string
    guard_name: string
    users_count: number
    permissions: Permission[]
    created_at: string | null
}

const props = defineProps<{
    roles: Role[]
}>()

const search = defineModel<string>('search', {
    default: '',
})

const filteredRoles = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    if (!keyword) {
        return props.roles
    }

    return props.roles.filter(role =>
        role.name.toLowerCase().includes(keyword)
    )
})

function deleteRole(role: Role) {
    if (role.name === 'Super Admin') {
        return
    }

    if (role.users_count > 0) {
        alert('Role masih digunakan oleh user.')
        return
    }

    if (confirm(`Apakah Anda yakin ingin menghapus role "${role.name}"?`)) {
        router.delete(`/roles/${role.id}`)
    }
}

function formatDate(value: string | null): string {
    if (!value) return '-'

    return new Date(value).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}
</script>

<template>
    <div class="space-y-6 p-4">
        <!-- Page Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Role Management
                </h1>

                <p class="text-muted-foreground">
                    Kelola role dan permission pengguna Inbound-CSA.
                </p>
            </div>

            <Button as-child>
                <Link href="/roles/create">
                    <Plus class="mr-2 size-4" />
                    Create Role
                </Link>
            </Button>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Total Roles
                    </p>

                    <ShieldCheck class="size-5 text-muted-foreground" />
                </div>

                <p class="mt-2 text-2xl font-semibold">
                    {{ props.roles.length }}
                </p>
            </div>

            <div class="rounded-xl border p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Total Assigned Users
                    </p>

                    <Users class="size-5 text-muted-foreground" />
                </div>

                <p class="mt-2 text-2xl font-semibold">
                    {{
                        props.roles.reduce(
                            (total, role) => total + role.users_count,
                            0
                        )
                    }}
                </p>
            </div>

            <div class="rounded-xl border p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Total Role Permissions
                    </p>

                    <KeyRound class="size-5 text-muted-foreground" />
                </div>

                <p class="mt-2 text-2xl font-semibold">
                    {{
                        props.roles.reduce(
                            (total, role) => total + role.permissions.length,
                            0
                        )
                    }}
                </p>
            </div>
        </div>

        <!-- Role Table -->
        <div class="rounded-xl border">
            <div class="flex flex-col gap-4 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold">
                        Roles
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Daftar role yang tersedia dalam sistem.
                    </p>
                </div>

                <div class="relative w-full sm:max-w-xs">
                    <Search
                        class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                    />

                    <Input
                        v-model="search"
                        placeholder="Search role..."
                        class="pl-9"
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr class="border-b">
                            <th class="px-4 py-3 text-left font-medium">
                                Role Name
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Permissions
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Users
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Created At
                            </th>

                            <th class="px-4 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="role in filteredRoles"
                            :key="role.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <ShieldCheck class="size-4 text-muted-foreground" />

                                    <span class="font-medium">
                                        {{ role.name }}
                                    </span>

                                    <span
                                        v-if="role.name === 'Super Admin'"
                                        class="rounded-md border px-2 py-0.5 text-xs"
                                    >
                                        System
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                {{ role.permissions.length }}
                            </td>

                            <td class="px-4 py-3">
                                {{ role.users_count }}
                            </td>

                            <td class="px-4 py-3 text-muted-foreground">
                                {{ formatDate(role.created_at) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                        >
                                            <MoreHorizontal class="size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/roles/${role.id}`">
                                                Show
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/roles/${role.id}/edit`">
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem
                                            v-if="role.name !== 'Super Admin'"
                                            class="text-destructive focus:text-destructive"
                                            @click="deleteRole(role)"
                                        >
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>

                        <tr v-if="filteredRoles.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                No roles found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t p-4 text-sm text-muted-foreground">
                Showing {{ filteredRoles.length }} of {{ props.roles.length }} roles
            </div>
        </div>
    </div>
</template>
