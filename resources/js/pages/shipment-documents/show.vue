<script setup lang="ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import shipmentDocuments from '@/routes/shipment-documents';

interface shipmentDocuments{
    shipment_id: string
    document_type: string
    document_name: string
    document_number: string
    document_date: Date
    document_file: string
    verification_status: string
}

interface shipments {
    bl_number: string;
    invoice_number: string;
}

const props = defineProps<{
    shipment: shipments
    document: shipmentDocuments
}>()

const form = useForm({
    shipment_id: props.shipment.bl_number,
    bl_number: props.shipment.bl_number,
    invoice_number: props.shipment.invoice_number,
    document_type: props.document.document_type,
    document_name: props.document.document_name,
    document_number: props.document.document_number,
    document_date: props.document.document_date.toISOString().slice(0, 10),
    document_file: props.document.document_file,
    verification_status: props.document.verification_status,
})

const statusLabel = (status: string | null) =>
    status
        ? status
            .toLowerCase()
            .split('_')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ')
        : '-'

const dateLabel = (date: string | null) => date ? date.slice(0, 10) : '-'

</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/shipment-documents"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
            <ArrowLeft class="h-4 w-4" />
            Back to Custom Document
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Custom Document
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new Custom Document.
            </p>
        </div>

        <!-- Form -->
        <Form @submit.prevent="" class="space-y-6">

            <!-- Custom Document InFormation -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Custom Document Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic Custom Document information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Custom Document Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Shipment Number</label>
                        <Input v-model="form.shipment_id" placeholder="Enter Document Code" disabled />
                    </div>

                    <!-- Shipment bl_number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Custom BL Number</label>
                        <Input v-model="form.bl_number" placeholder="Enter product name" disabled />
                    </div>
                    <!-- Custom Invoice Number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Custom Invoice Number</label>
                        <Input v-model="form.invoice_number" placeholder="Enter product name" disabled/>
                    </div>

                    <!-- Document Type -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Document Type</label>
                        <Input v-model="form.document_type" placeholder="Enter HS code" disabled/>
                    </div>
                    <!-- Document Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Document Name</label>
                        <Input v-model="form.document_name" placeholder="Enter import duty" disabled/>
                    </div>
                    <!-- Document Number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Document Number</label>
                        <Input v-model="form.document_number" placeholder="Enter facility" disabled/>
                    </div>
                    <!-- Document Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Document Date</label>
                        <Input v-model="form.document_date" placeholder="Enter product description" disabled/>
                    </div>
                    <!-- Document File -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Document File</label>
                        <Input v-model="form.document_file" placeholder="Enter brand" disabled/>
                    </div>
                    <!-- Verification Status -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Verification Status</label>
                        <Input v-model="form.verification_status" placeholder="Enter category" disabled/>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4 p-6">
                <Button type="button" variant="outline" as-child>
                    <Link href="/shipment-document/edit">
                        Edit
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Custom Document' }}
                </Button>
            </div>
        </Form>
    </div>
</template>

<style>
</style>