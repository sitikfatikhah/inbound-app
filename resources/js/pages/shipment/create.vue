<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

const form = useForm({
    shipment_number: '',
    shipment_date: '',

    bl_number: '',
    vessel_name: '',
    voyage_number: '',

    etd: '',
    eta: '',

    status: 'pending',
    tracking_status: 'pending',

    company_id: '',
    forwarder_id: '',
    shipping_line_id: '',
    port_of_loading_id: '',
    port_of_discharge_id: '',
    warehouse_id: '',
})

function submit() {
    form.post('/shipment')
}
</script>

<template>
    <div class="w-full max-w-6xl space-y-6">

        <!-- Header -->
        <div>
            <Link
                href="/shipment"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Shipment
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Shipment
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new shipment record.
            </p>
        </div>

        <!-- Form -->
        <form
            @submit.prevent="submit"
            class="space-y-6"
        >

            <!-- Shipment Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Shipment Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic shipment information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Shipment Number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipment Number
                        </label>

                        <Input
                            v-model="form.shipment_number"
                            placeholder="Enter shipment number"
                        />

                        <p
                            v-if="form.errors.shipment_number"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.shipment_number }}
                        </p>
                    </div>

                    <!-- Shipment Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipment Date
                        </label>

                        <Input
                            v-model="form.shipment_date"
                            type="date"
                        />
                    </div>

                    <!-- BL Number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            BL Number
                        </label>

                        <Input
                            v-model="form.bl_number"
                            placeholder="Enter BL number"
                        />
                    </div>

                    <!-- Vessel -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Vessel Name
                        </label>

                        <Input
                            v-model="form.vessel_name"
                            placeholder="Enter vessel name"
                        />
                    </div>

                    <!-- Voyage -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Voyage Number
                        </label>

                        <Input
                            v-model="form.voyage_number"
                            placeholder="Enter voyage number"
                        />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Status
                        </label>

                        <select
                            v-model="form.status"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="pending">
                                Pending
                            </option>

                            <option value="processing">
                                Processing
                            </option>

                            <option value="in_transit">
                                In Transit
                            </option>

                            <option value="completed">
                                Completed
                            </option>

                            <option value="cancelled">
                                Cancelled
                            </option>
                        </select>
                    </div>

                    <!-- Tracking -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Tracking Status
                        </label>

                        <select
                            v-model="form.tracking_status"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="pending">
                                Pending
                            </option>

                            <option value="in_transit">
                                In Transit
                            </option>

                            <option value="arrived">
                                Arrived
                            </option>

                            <option value="delivered">
                                Delivered
                            </option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Schedule -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Shipment Schedule
                    </h2>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2">

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            ETD
                        </label>

                        <Input
                            v-model="form.etd"
                            type="date"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            ETA
                        </label>

                        <Input
                            v-model="form.eta"
                            type="date"
                        />
                    </div>

                </div>
            </div>

            <!-- Related Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Related Information
                    </h2>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Company ID
                        </label>

                        <Input
                            v-model="form.company_id"
                            placeholder="Company ID"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Forwarder ID
                        </label>

                        <Input
                            v-model="form.forwarder_id"
                            placeholder="Forwarder ID"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipping Line ID
                        </label>

                        <Input
                            v-model="form.shipping_line_id"
                            placeholder="Shipping Line ID"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Port of Loading ID
                        </label>

                        <Input
                            v-model="form.port_of_loading_id"
                            placeholder="Port of Loading ID"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Port of Discharge ID
                        </label>

                        <Input
                            v-model="form.port_of_discharge_id"
                            placeholder="Port of Discharge ID"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Warehouse ID
                        </label>

                        <Input
                            v-model="form.warehouse_id"
                            placeholder="Warehouse ID"
                        />
                    </div>

                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">

                <Button
                    type="button"
                    variant="outline"
                    as-child
                >
                    <Link href="/shipment">
                        Cancel
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Saving...'
                            : 'Save Shipment'
                    }}
                </Button>

            </div>

        </form>
    </div>
</template>