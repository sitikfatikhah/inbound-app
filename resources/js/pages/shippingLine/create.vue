<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    shipping_line_name: '',
    contact_person: '',
    email: '',
    phone: '',
    website: '',
    is_active: '',
})
const handleSubmit = () => {
    form.post('/shippingLine', {
        onSuccess: () => {
            console.log('Shipping Line berhasil disimpan');
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

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Shipping Line
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new shipping line.
            </p>
        </div>

        <!-- Form -->
        <Form @submit.prevent="handleSubmit" class="space-y-6">

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
                        <Input v-model="form.shipping_line_name" placeholder="Enter shipping line name" />
                    </div>

                    <!-- contact_person -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="form.contact_person" placeholder="Enter contact person" />
                    </div>

                    <!-- email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="form.email" placeholder="Enter email address" />
                    </div>
                    <!-- phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="form.phone" placeholder="Enter phone number" />
                    </div>
                    <!-- website -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Website</label>
                        <Input v-model="form.website" placeholder="Enter website URL" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="form.is_active" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
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