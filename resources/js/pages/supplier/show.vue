<script setup lang="ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

interface Supplier {
    id: number;
    supplier_code: string;
    supplier_name: string;
    country: string;
    address: string;
    contact_person: string;
    email: string;
    phone: string;
    is_active: boolean;
}
const props = defineProps<{
    supplier: Supplier
}>()

const form = useForm({
    supplier_code: props.supplier.supplier_code || '',
    supplier_name: props.supplier.supplier_name || '',
    country: props.supplier.country || '',
    address: props.supplier.address || '',
    contact_person: props.supplier.contact_person || '',
    email: props.supplier.email || '',
    phone: props.supplier.phone || '',
    is_active: props.supplier.is_active || false,
})

const handleSubmit = () => {
    form.put(`/suppliers/${props.supplier.id}`, {
        onSuccess: () => {
            console.log('Supplier berhasil diedit');
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
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                        View Supplier
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        View supplier details.
                    </p>
                </div>
                <!-- Edit -->
                <Button as-child>
                    <Link :href="`/suppliers/${props.supplier.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>

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
                        <Input v-model="form.supplier_code" placeholder="Enter supplier code" disabled />
                        <p v-if="form.errors.supplier_code" class="text-sm text-destructive">
                            {{ form.errors.supplier_code }}
                        </p>
                    </div>

                    <!-- Supplier Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Supplier Name</label>
                        <Input v-model="form.supplier_name" placeholder="Enter supplier name" disabled />
                        <p v-if="form.errors.supplier_name" class="text-sm text-destructive">
                            {{ form.errors.supplier_name }}
                        </p>
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Address</label>
                        <Input v-model="form.address" placeholder="Enter supplier address" disabled />
                        <p v-if="form.errors.address" class="text-sm text-destructive">
                            {{ form.errors.address }}
                        </p>
                    </div>
                    <!-- Country -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country</label>
                        <Input v-model="form.country" placeholder="Enter supplier country" disabled />
                        <p v-if="form.errors.country" class="text-sm text-destructive">
                            {{ form.errors.country }}
                        </p>
                    </div>
                    <!-- Contact Person -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="form.contact_person" placeholder="Enter contact person" disabled />
                        <p v-if="form.errors.contact_person" class="text-sm text-destructive">
                            {{ form.errors.contact_person }}
                        </p>
                    </div>
                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="form.email" placeholder="Enter email address" disabled />
                        <p v-if="form.errors.email" class="text-sm text-destructive">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <!-- Phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="form.phone" placeholder="Enter phone number" disabled />
                        <p v-if="form.errors.phone" class="text-sm text-destructive">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="form.is_active" disabled />
                        <p v-if="form.errors.is_active" class="text-sm text-destructive">
                            {{ form.errors.is_active }}
                        </p>
                    </div>
                </div>
            </div>
        </Form>
    </div>
</template>

<style></style>