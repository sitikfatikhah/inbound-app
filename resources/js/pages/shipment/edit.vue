<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

interface Related { id: number | string; [key: string]: string | number }
interface Shipment {
    id: number | string
    shipment_number: string | null
    forwarder_id: number | string | null
    shipping_line_id: number | string | null
    port_of_loading_id: number | string | null
    port_of_discharge_id: number | string | null
    warehouse_id: number | string | null
    bl_number: string | null
    vessel_name: string | null
    voyage_number: string | null
    etd: string | null
    eta: string | null
    atd: string | null
    ata: string | null
    status_document: string | null
    tracking_status: string | null
}

const props = defineProps<{
    shipment: Shipment
    forwarders?: Related[]
    shippingLines?: Related[]
    ports?: Related[]
    warehouses?: Related[]
}>()

const form = useForm({
    shipment_number: props.shipment.shipment_number ?? '',
    forwarder_id: String(props.shipment.forwarder_id ?? ''),
    shipping_line_id: String(props.shipment.shipping_line_id ?? ''),
    port_of_loading_id: String(props.shipment.port_of_loading_id ?? ''),
    port_of_discharge_id: String(props.shipment.port_of_discharge_id ?? ''),
    warehouse_id: String(props.shipment.warehouse_id ?? ''),
    bl_number: props.shipment.bl_number ?? '',
    vessel_name: props.shipment.vessel_name ?? '',
    voyage_number: props.shipment.voyage_number ?? '',
    etd: props.shipment.etd?.slice(0, 10) ?? '',
    eta: props.shipment.eta?.slice(0, 10) ?? '',
    atd: props.shipment.atd?.slice(0, 10) ?? '',
    ata: props.shipment.ata?.slice(0, 10) ?? '',
    status_document: props.shipment.status_document ?? 'purchase order created',
    tracking_status: props.shipment.tracking_status ?? 'not shipped',
    location: '',
    country: '',
    remarks: '',
})

const statusOptions = ['purchase order created', 'booked', 'in transit', 'arrived at port', 'customs process', 'customs cleared', 'delivered', 'completed', 'on hold', 'cancelled']
const trackingStatusOptions = ['not shipped', 'gate out empty', 'gate in', 'loaded on vessel', 'vessel departed', 'in transit', 'vessel arrived', 'discharged', 'delivered', 'gate out for delivery', 'empty container return', 'completed', 'on hold', 'cancelled']
const submit = () => form.put(`/shipments/${props.shipment.id}`)
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <div><Link href="/shipments" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"><ArrowLeft class="h-4 w-4" /> Back to Shipments</Link><h1 class="mt-4 text-2xl font-semibold">Edit Shipment</h1><p class="text-sm text-muted-foreground">Update shipment information and record a status change when needed.</p></div>
        <form class="space-y-6" @submit.prevent="submit">
            <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Shipment Information</h2></div><div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="space-y-2"><label>Shipment Number</label><Input v-model="form.shipment_number" readonly /></div>
                <div class="space-y-2"><label>BL Number</label><Input v-model="form.bl_number" /></div><div class="space-y-2"><label>Vessel Name</label><Input v-model="form.vessel_name" /></div><div class="space-y-2"><label>Voyage Number</label><Input v-model="form.voyage_number" /></div>
                <div class="space-y-2"><label>Status Document</label><select v-model="form.status_document" class="field"><option v-for="item in statusOptions" :key="item" :value="item">{{ item }}</option></select></div><div class="space-y-2"><label>Tracking Status</label><select v-model="form.tracking_status" class="field"><option v-for="item in trackingStatusOptions" :key="item" :value="item">{{ item }}</option></select></div>
            </div></section>
            <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Shipment Schedule</h2></div><div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-4"><div class="space-y-2"><label>ETD</label><Input v-model="form.etd" type="date" /></div><div class="space-y-2"><label>ETA</label><Input v-model="form.eta" type="date" /></div><div class="space-y-2"><label>Actual Departure (ATD)</label><Input v-model="form.atd" type="date" /></div><div class="space-y-2"><label>Actual Arrival (ATA)</label><Input v-model="form.ata" type="date" /></div></div></section>
            <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Related Information</h2></div><div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="space-y-2"><label>Forwarder</label><select v-model="form.forwarder_id" class="field"><option v-for="item in forwarders" :key="item.id" :value="String(item.id)">{{ item.forwarder_name }}</option></select></div><div class="space-y-2"><label>Shipping Line</label><select v-model="form.shipping_line_id" class="field"><option v-for="item in shippingLines" :key="item.id" :value="String(item.id)">{{ item.shipping_line_name }}</option></select></div><div class="space-y-2"><label>Port of Loading</label><select v-model="form.port_of_loading_id" class="field"><option v-for="item in ports" :key="item.id" :value="String(item.id)">{{ item.port_name }}</option></select></div><div class="space-y-2"><label>Port of Discharge</label><select v-model="form.port_of_discharge_id" class="field"><option v-for="item in ports" :key="item.id" :value="String(item.id)">{{ item.port_name }}</option></select></div><div class="space-y-2"><label>Warehouse</label><select v-model="form.warehouse_id" class="field"><option v-for="item in warehouses" :key="item.id" :value="String(item.id)">{{ item.warehouse_name }}</option></select></div><div class="space-y-2"><label>History Location</label><Input v-model="form.location" /></div><div class="space-y-2"><label>Country</label><Input v-model="form.country" /></div><div class="space-y-2 md:col-span-2"><label>History Remarks</label><textarea v-model="form.remarks" class="field min-h-24" /></div>
            </div></section>
            <div class="flex justify-end gap-3"><Button type="button" variant="outline" as-child><Link href="/shipments">Cancel</Link></Button><Button type="submit" :disabled="form.processing">{{ form.processing ? 'Updating...' : 'Update Shipment' }}</Button></div>
        </form>
    </div>
</template>

<style scoped>
.field { display: flex; height: 2.5rem; width: 100%; border-radius: 0.375rem; border: 1px solid hsl(var(--border)); background: hsl(var(--background)); padding: 0.5rem 0.75rem; font-size: 0.875rem; }
</style>
