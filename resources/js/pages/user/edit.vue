
<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: string[];
}

interface Props {
    user: User;
    availableRoles: string[];
}

const props = defineProps<Props>();

// Convert database datetime to datetime-local format
const formatDateTimeLocal = (
    value: string | null,
): string => {
    if (!value) {
        return '';
    }

    return value.replace(' ', 'T').slice(0, 16);
};

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    email_verified_at: formatDateTimeLocal(
        props.user.email_verified_at,
    ),
    password: '',
    roles: [...props.user.roles],
});

const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        email_verified_at: data.email_verified_at || null,
        password: data.password || null,
    })).put(`/user/${props.user.id}`);
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

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Edit User
            </h1>

            <p class="text-sm text-muted-foreground">
                Update user account information and access permissions.
            </p>
        </div>

        <form
            @submit.prevent="handleSubmit"
            class="space-y-6"
        >
            <!-- User Information -->
            <div class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        User Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Update the basic account information.
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
                            v-model="form.name"
                            autocomplete="name"
                        />

                        <p
                            v-if="form.errors.name"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email">
                            Email
                        </Label>

                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                        />

                        <p
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Email Verification -->
                    <div class="space-y-2">
                        <Label for="email_verified_at">
                            Email Verified At
                        </Label>

                        <Input
                            id="email_verified_at"
                            v-model="form.email_verified_at"
                            type="datetime-local"
                        />

                        <p class="text-xs text-muted-foreground">
                            Leave empty if the email is not verified.
                        </p>

                        <p
                            v-if="form.errors.email_verified_at"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email_verified_at }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password">
                            New Password
                        </Label>

                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="Leave empty to keep current password"
                        />

                        <p
                            v-if="form.errors.password"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Role & Permission -->
            <div class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Role & Access Control
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Assign one or more roles to this user.
                        Permissions are inherited from the assigned roles.
                    </p>
                </div>

                <div class="space-y-2 p-6">
                    <Label for="roles">
                        User Roles
                    </Label>

                    <select
                        id="roles"
                        v-model="form.roles"
                        multiple
                        class="flex min-h-32 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option
                            v-for="role in props.availableRoles"
                            :key="role"
                            :value="role"
                        >
                            {{ role }}
                        </option>
                    </select>

                    <p class="text-xs text-muted-foreground">
                        Hold Ctrl (Windows) or Command (Mac)
                        to select multiple roles.
                    </p>

                    <p
                        v-if="form.errors.roles"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.roles }}
                    </p>

                    <p
                        v-if="form.errors['roles.0']"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors['roles.0'] }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Button
                    type="button"
                    variant="outline"
                    as-child
                >
                    <Link href="/users">
                        Cancel
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Saving...'
                            : 'Save Changes'
                    }}
                </Button>
            </div>
        </form>
    </div>
</template>
