<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface ShippingLine {
    id: number;
    shipping_line_name: string;
    contact_person: string;
    email: string;
    phone: string;
    website: string;
    is_active: boolean;
}
const props = defineProps<{
    shippingLine: ShippingLine
}>()

const form = useForm({
    shipping_line_name: props.shippingLine.shipping_line_name,
    contact_person: props.shippingLine.contact_person,
    email: props.shippingLine.email,
    phone: props.shippingLine.phone,
    website: props.shippingLine.website,
    is_active: props.shippingLine.is_active as boolean,
})
const handleSubmit = () => {
    form.put(`/shippingLine/${props.shippingLine.id}`, {
        onSuccess: () => {
            console.log('Shipping Line berhasil diperbarui');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
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
                        View the details of this shipping line.
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
        <Form class="space-y-6">

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
                        <Input v-model="props.shippingLine.shipping_line_name" placeholder="Enter shipping line name"
                            readonly />
                    </div>

                    <!-- contact_person -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="props.shippingLine.contact_person" placeholder="Enter contact person" />
                    </div>

                    <!-- email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="props.shippingLine.email" placeholder="Enter email address" />
                    </div>
                    <!-- phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="props.shippingLine.phone" placeholder="Enter phone number" />
                    </div>
                    <!-- website -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Website</label>
                        <Input v-model="props.shippingLine.website" placeholder="Enter website URL" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="props.shippingLine.is_active" />
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
        </Form>
    </div>
</template>

<style></style>