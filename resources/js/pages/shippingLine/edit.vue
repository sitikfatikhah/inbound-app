<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { update as updateShippingLine } from '@/actions/App/Http/Controllers/ShippingLineController';

interface ShippingLine {
    id: number;
    shipping_line_name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
    website: string | null;
    is_active: boolean;
}
const props = defineProps<{
    shippingLine: ShippingLine
}>()

const form = useForm({
    shipping_line_name: props.shippingLine.shipping_line_name,
    contact_person: props.shippingLine.contact_person ?? '',
    email: props.shippingLine.email ?? '',
    phone: props.shippingLine.phone ?? '',
    website: props.shippingLine.website ?? '',
    is_active: Boolean(props.shippingLine.is_active),
})
const handleSubmit = () => {
    form.put(updateShippingLine.url(props.shippingLine.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/shippingLine"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Shipping Lines
            </Link>

            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                        {{ props.shippingLine.shipping_line_name }}
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        Edit the details of this shipping line.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="`/shippingLine/${props.shippingLine.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>

        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-6">

            <!-- Shipping Line Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Shipping Line Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic shipping line information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- country -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Shipping Line Name</label>
                        <Input v-model="form.shipping_line_name" placeholder="Enter shipping line name" readonly />
                        <p v-if="form.errors.shipping_line_name" class="text-sm text-destructive">
                            {{ form.errors.shipping_line_name }}
                        </p>
                    </div>

                    <!-- contact_person -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="form.contact_person" placeholder="Enter contact person" />
                        <p v-if="form.errors.contact_person" class="text-sm text-destructive">
                            {{ form.errors.contact_person }}
                        </p>
                    </div>

                    <!-- email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="form.email" placeholder="Enter email address" />
                        <p v-if="form.errors.email" class="text-sm text-destructive">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <!-- phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="form.phone" placeholder="Enter phone number" />
                        <p v-if="form.errors.phone" class="text-sm text-destructive">
                            {{ form.errors.phone }}
                        </p>
                    </div>
                    <!-- website -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Website</label>
                        <Input v-model="form.website" placeholder="Enter website URL" />
                        <p v-if="form.errors.website" class="text-sm text-destructive">
                            {{ form.errors.website }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="form.is_active" />
                        <p v-if="form.errors.is_active" class="text-sm text-destructive">
                            {{ form.errors.is_active }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-4 p-6">
                <Button type="button" variant="outline" as-child>
                    <Link href="/shippingLine">
                        Cancel
                    </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Shipping Line' }}
                </Button>
            </div>
        </form>
    </div>
</template>

<style></style>