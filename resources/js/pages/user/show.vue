
<script setup lang="ts">
import { ArrowLeft, UserRound } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: string[];
    created_at: string | null;
    updated_at: string | null;
}

const props = defineProps<{
    user: User;
}>();

const formatDateTime = (value: string | null): string => {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <!-- Header -->
        <div>
            <Link
                href="/user"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>

            <div class="mt-4 flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <UserRound class="h-6 w-6" />

                        <h1 class="text-2xl font-semibold tracking-tight">
                            View User
                        </h1>
                    </div>

                    <p class="text-sm text-muted-foreground">
                        View user account information and access roles.
                    </p>
                </div>

                <Button as-child>
                    <Link :href="`/user/${props.user.id}/edit`">
                        Edit User
                    </Link>
                </Button>
            </div>
        </div>

        <!-- User Information -->
        <div class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">
                    User Information
                </h2>

                <p class="text-sm text-muted-foreground">
                    The account information stored for this user.
                </p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Name -->
                <div class="space-y-2">
                    <Label for="name">
                        Name
                    </Label>

                    <Input
                        id="name"
                        :model-value="props.user.name"
                        disabled
                    />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email">
                        Email
                    </Label>

                    <Input
                        id="email"
                        :model-value="props.user.email"
                        disabled
                    />
                </div>

                <!-- Email Verification -->
                <div class="space-y-2">
                    <Label for="email_verified_at">
                        Email Verified At
                    </Label>

                    <Input
                        id="email_verified_at"
                        :model-value="formatDateTime(props.user.email_verified_at)"
                        disabled
                    />
                </div>

                <!-- Roles -->
                <div class="space-y-2">
                    <Label>
                        User Roles
                    </Label>

                    <div
                        v-if="props.user.roles?.length"
                        class="flex min-h-10 flex-wrap items-center gap-2"
                    >
                        <span
                            v-for="role in props.user.roles"
                            :key="role"
                            class="rounded-md border px-2.5 py-1 text-sm font-medium"
                        >
                            {{ role }}
                        </span>
                    </div>

                    <Input
                        v-else
                        model-value="No role assigned"
                        disabled
                    />
                </div>

                <!-- Created At -->
                <div class="space-y-2">
                    <Label for="created_at">
                        Created At
                    </Label>

                    <Input
                        id="created_at"
                        :model-value="formatDateTime(props.user.created_at)"
                        disabled
                    />
                </div>

                <!-- Updated At -->
                <div class="space-y-2">
                    <Label for="updated_at">
                        Last Updated
                    </Label>

                    <Input
                        id="updated_at"
                        :model-value="formatDateTime(props.user.updated_at)"
                        disabled
                    />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end">
            <Button
                variant="outline"
                as-child
            >
                <Link href="/user">
                    Back to Users
                </Link>
            </Button>
        </div>
    </div>
</template>
