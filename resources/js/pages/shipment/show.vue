<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Pencil } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'

interface Shipment {
    id: number | string

    shipment_number?: string | null
    shipment_date?: string | null

    bl_number?: string | null
    vessel_name?: string | null
    voyage_number?: string | null

    etd?: string | null
    eta?: string | null
    etd_actual?: string | null
    eta_actual?: string | null
    ata?: string | null

    status?: string | null
    tracking_status?: string | null

    company_id?: number | string | null
    forwarder_id?: number | string | null
    shipping_line_id?: number | string | null
    port_of_loading_id?: number | string | null
    port_of_discharge_id?: number | string | null
    warehouse_id?: number | string | null
}

const props = defineProps<{
    shipment: Shipment
}>()

function statusClass(status?: string | null) {
    switch (status?.toLowerCase()) {
        case 'success':
        case 'completed':
        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'processing':
        case 'in_transit':
        case 'in transit':
            return 'bg-blue-100 text-blue-700'

        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'failed':
        case 'cancelled':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}
</script>

<template>
    <div class="w-full space-y-6">

        <!-- Header -->
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <div class="mb-2">
                    <Link
                        href="/shipment"
                        class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Back to Shipment
                    </Link>
                </div>

                <h1 class="text-2xl font-semibold tracking-tight">
                    Shipment Detail
                </h1>

                <p class="text-sm text-muted-foreground">
                    View shipment information and tracking details.
                </p>
            </div>

            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link
                        :href="`/shipment/${props.shipment.id}/edit`"
                    >
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="rounded-lg border bg-card">

            <div class="border-b p-6">
                <h2 class="font-semibold">
                    Shipment Information
                </h2>

                <p class="text-sm text-muted-foreground">
                    Basic information about this shipment.
                </p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                <!-- Shipment Number -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Shipment Number
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.shipment_number || '-' }}
                    </p>
                </div>

                <!-- Shipment Date -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Shipment Date
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.shipment_date || '-' }}
                    </p>
                </div>

                <!-- BL Number -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        BL Number
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.bl_number || '-' }}
                    </p>
                </div>

                <!-- Vessel -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Vessel
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.vessel_name || '-' }}
                    </p>
                </div>

                <!-- Voyage -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Voyage Number
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.voyage_number || '-' }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Status
                    </p>

                    <div class="mt-2">
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                            :class="statusClass(props.shipment.status)"
                        >
                            {{ props.shipment.status || '-' }}
                        </span>
                    </div>
                </div>

                <!-- Tracking Status -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Tracking Status
                    </p>

                    <p class="mt-1 font-medium capitalize">
                        {{ props.shipment.tracking_status || '-' }}
                    </p>
                </div>

            </div>
        </div>

        <!-- Schedule -->
        <div class="rounded-lg border bg-card">

            <div class="border-b p-6">
                <h2 class="font-semibold">
                    Shipment Schedule
                </h2>

                <p class="text-sm text-muted-foreground">
                    Planned and actual shipment schedule.
                </p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                <!-- ETD -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        ETD
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.etd || '-' }}
                    </p>
                </div>

                <!-- ETA -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        ETA
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.eta || '-' }}
                    </p>
                </div>

                <!-- Actual ETD -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Actual ETD
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.etd_actual || '-' }}
                    </p>
                </div>

                <!-- Actual ETA -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        Actual ETA
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.eta_actual || '-' }}
                    </p>
                </div>

                <!-- ATA -->
                <div>
                    <p class="text-sm text-muted-foreground">
                        ATA
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.ata || '-' }}
                    </p>
                </div>

            </div>
        </div>

        <!-- Related Information -->
        <div class="rounded-lg border bg-card">

            <div class="border-b p-6">
                <h2 class="font-semibold">
                    Related Information
                </h2>

                <p class="text-sm text-muted-foreground">
                    Related entities associated with this shipment.
                </p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                <div>
                    <p class="text-sm text-muted-foreground">
                        Company
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.company_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Forwarder
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.forwarder_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Shipping Line
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.shipping_line_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Port of Loading
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.port_of_loading_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Port of Discharge
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.port_of_discharge_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted-foreground">
                        Warehouse
                    </p>

                    <p class="mt-1 font-medium">
                        {{ props.shipment.warehouse_id || '-' }}
                    </p>
                </div>

            </div>
        </div>

    </div>
</template>