<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'
import { computed } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

interface Option { id: number;[key: string]: string | number }

const props = defineProps<{
    shipment_number: string
    forwarders?: Option[]
    shippingLines?: Option[]
    ports?: Option[]
    warehouses?: Option[]
}>()

const form = useForm({
    shipment_number: props.shipment_number,
    forwarder_id: '',
    shipping_line_id: '',
    port_of_loading_id: '',
    port_of_discharge_id: '',
    warehouse_id: '',
    bl_number: '',
    vessel_name: '',
    voyage_number: '',
    etd: '',
    eta: '',
    atd: '',
    ata: '',
    status_document: 'purchase order created',
    tracking_status: 'not shipped',
    remarks: '',
})

const statusOptions = [
    'PL & INV created', 'BL Draft', 'BL Final', 'LCC created',
    'PTDI', 'sent document', 'draft PIB', 'Billing Bea Cukai', 'BPN', 'customs clearance', 'Green Lane - SPPB', 'Red Lane - SPJM','NPD', 'NHI', 'SPTNP','SPKTNP', 'SPBL','Gate Out', 'Others'
]
const trackingStatusOptions = [
    'not shipped', 'gate out empty', 'gate in', 'loaded on vessel', 'vessel departed',
    'in transit', 'vessel arrived', 'discharged', 'delivered', 'gate out for delivery',
    'empty container return', 'completed', 'on hold', 'cancelled',
]
const forwarderOptions = computed(() => props.forwarders ?? [])
const shippingLineOptions = computed(() => props.shippingLines ?? [])
const portOptions = computed(() => props.ports ?? [])
const warehouseOptions = computed(() => props.warehouses ?? [])

const submit = () => form.post('/shipments')
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <div>
            <Link href="/shipments"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" /> Back to Shipments
            </Link>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">Add Shipment</h1>
            <p class="text-sm text-muted-foreground">Create a shipment using the fields defined by the shipment table.
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">Shipment Information</h2>
                </div>
                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2"><label>Shipment Number</label><Input v-model="form.shipment_number"
                            readonly />
                        <p v-if="form.errors.shipment_number" class="text-sm text-destructive">{{
                            form.errors.shipment_number }}</p>
                    </div>
                    <div class="space-y-2"><label>BL Number</label><Input v-model="form.bl_number" />
                        <p v-if="form.errors.bl_number" class="text-sm text-destructive">{{ form.errors.bl_number }}</p>
                    </div>
                    <div class="space-y-2"><label>Vessel Name</label><Input v-model="form.vessel_name" /></div>
                    <div class="space-y-2"><label>Voyage Number</label><Input v-model="form.voyage_number" /></div>
                    <div class="space-y-2"><label>Status Document</label><select v-model="form.status_document"
                            class="field">
                            <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                        </select></div>
                    <div class="space-y-2"><label>Tracking Status</label><select v-model="form.tracking_status"
                            class="field">
                            <option v-for="status in trackingStatusOptions" :key="status" :value="status">{{ status }}
                            </option>
                        </select></div>
                </div>
            </section>

            <section class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">Shipment Schedule</h2>
                </div>
                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-2"><label>ETD</label><Input v-model="form.etd" type="date" /></div>
                    <div class="space-y-2"><label>ETA</label><Input v-model="form.eta" type="date" /></div>
                    <div class="space-y-2"><label>Actual Departure (ATD)</label><Input v-model="form.atd" type="date" />
                    </div>
                    <div class="space-y-2"><label>Actual Arrival (ATA)</label><Input v-model="form.ata" type="date" />
                    </div>
                </div>
            </section>

            <section class="rounded-lg border bg-card">
                <div class="border-b p-6">
                    <h2 class="font-semibold">Related Information</h2>
                </div>
                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2"><label>Forwarder</label><select v-model="form.forwarder_id" class="field">
                            <option value="" disabled>Select forwarder</option>
                            <option v-for="item in forwarderOptions" :key="item.id" :value="item.id">{{
                                item.forwarder_name }}</option>
                        </select></div>
                    <div class="space-y-2"><label>Shipping Line</label><select v-model="form.shipping_line_id"
                            class="field">
                            <option value="" disabled>Select shipping line</option>
                            <option v-for="item in shippingLineOptions" :key="item.id" :value="item.id">{{
                                item.shipping_line_name }}</option>
                        </select></div>
                    <div class="space-y-2"><label>Port of Loading</label><select v-model="form.port_of_loading_id"
                            class="field">
                            <option value="" disabled>Select port</option>
                            <option v-for="item in portOptions" :key="item.id" :value="item.id">{{ item.port_name }}
                            </option>
                        </select></div>
                    <div class="space-y-2"><label>Port of Discharge</label><select v-model="form.port_of_discharge_id"
                            class="field">
                            <option value="" disabled>Select port</option>
                            <option v-for="item in portOptions" :key="item.id" :value="item.id">{{ item.port_name }}
                            </option>
                        </select></div>
                    <div class="space-y-2"><label>Warehouse</label><select v-model="form.warehouse_id" class="field">
                            <option value="" disabled>Select warehouse</option>
                            <option v-for="item in warehouseOptions" :key="item.id" :value="item.id">{{
                                item.warehouse_name }}</option>
                        </select></div>
                    <div class="space-y-2 md:col-span-2"><label>Remarks</label><textarea v-model="form.remarks"
                            class="field min-h-24" /></div>
                </div>
            </section>

            <div class="flex justify-end gap-3"><Button type="button" variant="outline" as-child>
                    <Link href="/shipments">Cancel</Link>
                </Button><Button type="submit" :disabled="form.processing">{{ form.processing ? 'Saving...' : 'Save Shipment' }}</Button></div>
        </form>
    </div>
</template>

<style scoped>
.field {
    display: flex;
    height: 2.5rem;
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid hsl(var(--border));
    background: hsl(var(--background));
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
</style>
