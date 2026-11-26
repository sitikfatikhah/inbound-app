
<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    availableRoles: string[];
}

defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const handleSubmit = () => {
    form.post('/user', {
        preserveScroll: true,
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

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add User
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new user and assign a role.
            </p>
        </div>

        <!-- Form -->
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
                        Enter the basic user information.
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
                            placeholder="Enter user name"
                            autocomplete="name"
                            required
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
                            placeholder="Enter email address"
                            autocomplete="email"
                            required
                        />

                        <p
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <Label for="role">
                            Role
                        </Label>

                        <select
                            id="role"
                            v-model="form.role"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="" disabled>
                                Select user role
                            </option>

                            <option
                                v-for="role in availableRoles"
                                :key="role"
                                :value="role"
                            >
                                {{ role }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.role"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.role }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password">
                            Password
                        </Label>

                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Enter password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        />

                        <p
                            v-if="form.errors.password"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <Label for="password_confirmation">
                            Confirm Password
                        </Label>

                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirm password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        />

                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Button
                    type="button"
                    variant="outline"
                    as-child
                    :disabled="form.processing"
                >
                    <Link href="/user">
                        Cancel
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Saving...' : 'Save User' }}
                </Button>
            </div>
        </form>
    </div>
</template>
