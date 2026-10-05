<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
    email: '',
    email_verified_at: '',
    password: '',
});

const handleSubmit = () => form.post('/users');
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <!-- Header -->
        <div>
            <Link
                href="/users"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">Add User</h1>

            <p class="text-sm text-muted-foreground">Create a new user.</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- User Information -->
            <div class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">User Information</h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic user information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- users Number -->

                    <!-- Name -->
                    <div class="space-y-2">
                        <Label for="name" class="text-sm font-medium"
                            >Name</Label
                        >
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Enter user name"
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
                        <Label for="email" class="text-sm font-medium"
                            >Email</Label
                        >
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="Enter email address"
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Email Verified at -->
                    <div class="space-y-2">
                        <Label
                            for="email_verified_at"
                            class="text-sm font-medium"
                            >Email Verified At</Label
                        >
                        <Input
                            id="email_verified_at"
                            v-model="form.email_verified_at"
                            type="datetime-local"
                        />
                        <p
                            v-if="form.errors.email_verified_at"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email_verified_at }}
                        </p>
                    </div>
                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password" class="text-sm font-medium"
                            >Password</Label
                        >
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Enter password"
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

            <!-- Actions -->
            <div class="flex justify-end gap-4 p-6">
                <Button type="button" variant="outline" as-child>
                    <Link href="/users"> Cancel </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save User' }}
                </Button>
            </div>
        </form>
    </div>
</template>

<style></style>
