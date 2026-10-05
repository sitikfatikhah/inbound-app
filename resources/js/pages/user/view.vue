<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
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
            <div class="mt-4 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        View User
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        View user details.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="`/users/${props.user.id}/edit`">Edit</Link>
                </Button>
            </div>
        </div>

        <div class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">User Information</h2>
                <p class="text-sm text-muted-foreground">
                    The account information stored for this user.
                </p>
            </div>
            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input id="name" :model-value="props.user.name" disabled />
                </div>
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        :model-value="props.user.email"
                        disabled
                    />
                </div>
                <div class="space-y-2">
                    <Label for="email_verified_at">Email Verified At</Label>
                    <Input
                        id="email_verified_at"
                        :model-value="props.user.email_verified_at || '-'"
                        disabled
                    />
                </div>
            </div>
        </div>
    </div>
</template>
