<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

interface Shipment {
    id: number | string

    shipment_number?: string | null
    shipment_date?: string | null

    bl_number?: string | null
    vessel_name?: string | null
    voyage_number?: string | null

    etd?: string | null
    eta?: string | null

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

const form = useForm({
    shipment_number: props.shipment.shipment_number ?? '',
    shipment_date: props.shipment.shipment_date ?? '',

    bl_number: props.shipment.bl_number ?? '',
    vessel_name: props.shipment.vessel_name ?? '',
    voyage_number: props.shipment.voyage_number ?? '',

    etd: props.shipment.etd ?? '',
    eta: props.shipment.eta ?? '',

    status: props.shipment.status ?? 'pending',
    tracking_status:
        props.shipment.tracking_status ?? 'pending',

    company_id: props.shipment.company_id?.toString() ?? '',
    forwarder_id:
        props.shipment.forwarder_id?.toString() ?? '',
    shipping_line_id:
        props.shipment.shipping_line_id?.toString() ?? '',
    port_of_loading_id:
        props.shipment.port_of_loading_id?.toString() ?? '',
    port_of_discharge_id:
        props.shipment.port_of_discharge_id?.toString() ?? '',
    warehouse_id:
        props.shipment.warehouse_id?.toString() ?? '',
})

function submit() {
    form.put(`/shipment/${props.shipment.id}`)
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

            <h1 class="mt-4 text-2xl font-semibold">
                Edit Shipment
            </h1>

            <p class="text-sm text-muted-foreground">
                Update shipment information.
            </p>
        </div>

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
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipment Number
                        </label>

                        <Input
                            v-model="form.shipment_number"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipment Date
                        </label>

                        <Input
                            v-model="form.shipment_date"
                            type="date"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            BL Number
                        </label>

                        <Input
                            v-model="form.bl_number"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Vessel Name
                        </label>

                        <Input
                            v-model="form.vessel_name"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Voyage Number
                        </label>

                        <Input
                            v-model="form.voyage_number"
                        />
                    </div>

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

            <!-- Related -->
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

                        <Input v-model="form.company_id" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Forwarder ID
                        </label>

                        <Input v-model="form.forwarder_id" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Shipping Line ID
                        </label>

                        <Input v-model="form.shipping_line_id" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Port of Loading ID
                        </label>

                        <Input v-model="form.port_of_loading_id" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Port of Discharge ID
                        </label>

                        <Input v-model="form.port_of_discharge_id" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            Warehouse ID
                        </label>

                        <Input v-model="form.warehouse_id" />
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
                            ? 'Updating...'
                            : 'Update Shipment'
                    }}
                </Button>

            </div>

        </form>
    </div>
</template>