<script setup lang="ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    id: '',
    supplier_code: '',
    supplier_name: '',
    country: '',
    address: '',
    contact_person: '',
    email: '',
    phone: '',
    is_active: false as boolean,
})

const handleSubmit = () => {
    form.post('/suppliers', {
        onSuccess: () => {
            console.log('Supplier berhasil disimpan');
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
            <Link href="/suppliers"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Suppliers
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Supplier
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new supplier.
            </p>
        </div>

        <!-- Form -->
        <Form @submit="handleSubmit" class="space-y-6">

            <!-- Supplier Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Supplier Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic supplier information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Supplier Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Supplier Code</label>
                        <Input v-model="form.supplier_code" placeholder="Enter supplier code" />
                    </div>

                    <!-- Supplier Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Supplier Name</label>
                        <Input v-model="form.supplier_name" placeholder="Enter supplier name" />
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Address</label>
                        <Input v-model="form.address" placeholder="Enter supplier address" />
                    </div>
                    <!-- Country -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country</label>
                        <Input v-model="form.country" placeholder="Enter supplier country" />
                    </div>
                    <!-- Contact Person -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="form.contact_person" placeholder="Enter contact person" />
                    </div>
                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="form.email" placeholder="Enter email address" />
                    </div>
                    <!-- Phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="form.phone" placeholder="Enter phone number" />
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
                    <Link href="/suppliers">
                        Cancel
                    </Link>
                </Button>

                <form @submit.prevent="handleSubmit">
                    <!-- field supplier -->

                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Supplier' }}
                    </Button>
                </form>
            </div>
        </Form>
    </div>
</template>

<style></style>