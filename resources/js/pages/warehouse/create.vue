<script setup lang = "ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    warehouse_code: '',
    warehouse_name: '',
    address: '',
    contact_person: '',
    email: '',
    phone: '',
    is_active: false,
})

function handleSubmit() {
    form.post('/warehouses', {
        onSuccess: () => {
            console.log('Warehouse berhasil disimpan');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    })
}
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/warehouses"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
            <ArrowLeft class="h-4 w-4" />
            Back to Warehouses
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Warehouse
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new warehouse.
            </p>
        </div>

        <!-- Form -->
        <Form @submit.prevent="handleSubmit" class="space-y-6">

            <!-- Warehouse Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Warehouse Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic warehouse information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Warehouse Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Warehouse Code</label>
                        <Input v-model="form.warehouse_code" placeholder="Enter warehouse code" />
                    </div>

                    <!-- Warehouse Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Warehouse Name</label>
                        <Input v-model="form.warehouse_name" placeholder="Enter warehouse name" />
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Address</label>
                        <Input v-model="form.address" placeholder="Enter warehouse address" />
                    </div>
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
                    <Link href="/warehouses">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Warehouse' }}
                </Button>
            </div>
        </Form>
    </div>
</template>

<style>

</style>