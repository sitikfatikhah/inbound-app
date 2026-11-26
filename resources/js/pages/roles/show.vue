
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'

interface Permission {
    id: number
    name: string
}

interface User {
    id: number
    name: string
    email: string
}

interface Role {
    id: number
    name: string
    guard_name: string
    created_at: string | null
    updated_at: string | null
    permissions: Permission[]
    users: User[]
}

const props = defineProps<{
    role: Role
}>()

function formatDate(value: string | null): string {
    if (!value) return '-'

    return new Date(value).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    })
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row">
            <div>
                <h1 class="text-2xl font-semibold">
                    Role Details
                </h1>

                <p class="text-muted-foreground">
                    Informasi role dan permission yang diberikan.
                </p>
            </div>

            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/roles">
                        Back
                    </Link>
                </Button>

                <Button as-child>
                    <Link :href="`/roles/${role.id}/edit`">
                        Edit Role
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Role Information -->
        <div class="rounded-xl border p-6">
            <h2 class="mb-4 text-lg font-semibold">
                Role Information
            </h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-muted-foreground">
                        Role Name
                    </p>
                    <p class="font-medium">
                        {{ role.name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Guard
                    </p>
                    <p class="font-medium">
                        {{ role.guard_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Created At
                    </p>
                    <p class="font-medium">
                        {{ formatDate(role.created_at) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Updated At
                    </p>
                    <p class="font-medium">
                        {{ formatDate(role.updated_at) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="rounded-xl border p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">
                    Permissions
                </h2>

                <span class="text-sm text-muted-foreground">
                    {{ role.permissions.length }} permissions
                </span>
            </div>

            <div
                v-if="role.permissions.length"
                class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="permission in role.permissions"
                    :key="permission.id"
                    class="rounded-md border px-3 py-2 text-sm"
                >
                    {{ permission.name }}
                </div>
            </div>

            <p v-else class="text-sm text-muted-foreground">
                Role ini belum memiliki permission.
            </p>
        </div>

        <!-- Assigned Users -->
        <div class="rounded-xl border p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">
                    Assigned Users
                </h2>

                <span class="text-sm text-muted-foreground">
                    {{ role.users.length }} users
                </span>
            </div>

            <div
                v-if="role.users.length"
                class="space-y-3"
            >
                <div
                    v-for="user in role.users"
                    :key="user.id"
                    class="flex flex-col justify-between gap-1 rounded-md border p-3 sm:flex-row"
                >
                    <div>
                        <p class="font-medium">
                            {{ user.name }}
                        </p>

                        <p class="text-sm text-muted-foreground">
                            {{ user.email }}
                        </p>
                    </div>
                </div>
            </div>

            <p v-else class="text-sm text-muted-foreground">
                Belum ada user yang menggunakan role ini.
            </p>
        </div>
    </div>
</template>
