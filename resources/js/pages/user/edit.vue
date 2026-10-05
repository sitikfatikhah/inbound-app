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
}

const props = defineProps<{ user: User }>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    email_verified_at: props.user.email_verified_at ?? '',
    password: '',
});

const handleSubmit = () => form.put(`/users/${props.user.id}`);
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <div>
            <Link
                href="/users"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Edit User
            </h1>
            <p class="text-sm text-muted-foreground">
                Update the user account information.
            </p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">User Information</h2>
                    <p class="text-sm text-muted-foreground">
                        Leave the password empty to keep it unchanged.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" />
                        <p
                            v-if="form.errors.name"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="email_verified_at">Email Verified At</Label>
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
                    <div class="space-y-2">
                        <Label for="password">New Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
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

            <div class="flex justify-end gap-4">
                <Button type="button" variant="outline" as-child>
                    <Link href="/users">Cancel</Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </Button>
            </div>
        </form>
    </div>
</template>
