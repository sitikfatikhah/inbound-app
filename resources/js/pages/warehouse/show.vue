<script setup lang="ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

interface Warehouses {
    id: number
    warehouse_code: string
    warehouse_name: string
    address: string
    contact_person: string
    email: string
    phone: string
    is_active: boolean
}

const props = defineProps<{
    warehouse: Warehouses
}>()

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
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                        View Warehouse
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        View the details of the selected warehouse.
                    </p>
                </div>
                <!-- Edit -->
                <Button as-child>
                    <Link :href="`/warehouses/${props.warehouse.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>

        </div>

        <!-- Form -->
        <Form class="space-y-6">

            <!-- Warehouse Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Warehouse Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        View the basic warehouse information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Warehouse Id -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Warehouse Id</label>
                        <Input v-model="props.warehouse.id" placeholder="Enter warehouse id" readonly />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Warehouse Code</label>
                        <Input v-model="props.warehouse.warehouse_code" placeholder="Enter warehouse code" readonly />
                    </div>

                    <!-- Warehouse Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Warehouse Name</label>
                        <Input v-model="props.warehouse.warehouse_name" placeholder="Enter warehouse name" readonly />
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Address</label>
                        <Input v-model="props.warehouse.address" placeholder="Enter warehouse address" readonly />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contact Person</label>
                        <Input v-model="props.warehouse.contact_person" placeholder="Enter contact person" readonly />
                    </div>
                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="props.warehouse.email" placeholder="Enter email address" readonly />
                    </div>
                    <!-- Phone -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone</label>
                        <Input v-model="props.warehouse.phone" placeholder="Enter phone number" readonly />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="props.warehouse.is_active" disabled />
                    </div>
                </div>
            </div>
        </Form>
    </div>
</template>

<style></style>