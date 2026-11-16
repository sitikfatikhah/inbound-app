<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { index, update } from '@/actions/App/Http/Controllers/TrackingStatusController';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface TrackingStatus {
    id: number;
    category: string;
    code: string;
    name: string;
    is_active: boolean;
}

const props = defineProps<{
    tracking_statuses: TrackingStatus;
}>();

const form = useForm({
    category: props.tracking_statuses.category,
    code: props.tracking_statuses.code,
    name: props.tracking_statuses.name,
    is_active: props.tracking_statuses.is_active,
});

const handleSubmit = () => {
    form.put(update.url(props.tracking_statuses.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <Head title="Edit Status" />

        <main class="mx-auto w-full space-y-6 p-2 sm:p-8">
            <header class="space-y-2">
                <Link
                    :href="index.url()"
                    class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Index Status
                </Link>
                <div class="space-y-2">
                    <h1 class="pt-4 text-2xl font-semibold">Edit Status</h1>
                    <p class="text-sm text-muted-foreground">Edit your status</p>
                </div>
            </header>

            <section class="overflow-x-auto rounded-md border p-4">
                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <div class="grid grid-cols-1 gap-4 rounded-lg border bg-card p-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="category" class="text-sm font-medium">Category</Label>
                            <Input id="category" v-model="form.category" />
                            <p v-if="form.errors.category" class="text-sm text-destructive">
                                {{ form.errors.category }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <Label for="code" class="text-sm font-medium">Code</Label>
                            <Input id="code" v-model="form.code" />
                            <p v-if="form.errors.code" class="text-sm text-destructive">
                                {{ form.errors.code }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-medium">Name</Label>
                            <Input id="name" v-model="form.name" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <Label for="is_active" class="text-sm font-medium">Active</Label>
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-input"
                            />
                            <p v-if="form.errors.is_active" class="text-sm text-destructive">
                                {{ form.errors.is_active }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 p-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="index.url()">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Status' }}
                        </Button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>
