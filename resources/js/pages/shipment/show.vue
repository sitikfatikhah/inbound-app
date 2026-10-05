<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Pencil } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

interface Related { id: number | string; [key: string]: string | number }
interface Milestone { id: number | string; milestone_name: string; milestone_date: string | null; planned_date: string | null; actual_date: string | null; status: string; remarks: string | null }
interface History { id: number | string; status: string | null; status_date: string | null; location: string | null; country: string | null; remarks: string | null }
interface Shipment {
    id: number | string; shipment_number: string | null; forwarder?: Related | null; shipping_line?: Related | null; port_of_loading?: Related | null; port_of_discharge?: Related | null; warehouse?: Related | null; bl_number: string | null; vessel_name: string | null; voyage_number: string | null; etd: string | null; eta: string | null; atd: string | null; ata: string | null; status_document: string | null; tracking_status: string | null; milestones?: Milestone[]; status_histories?: History[]
}
const props = defineProps<{ shipment: Shipment }>()
const dateLabel = (value: string | null) => value?.slice(0, 10) ?? '-'
const dateTimeLabel = (value: string | null) => value ? value.replace('T', ' ').slice(0, 16) : '-'
const label = (value: string | null) => value ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()) : '-'
const statusClass = (value: string | null) => ['completed', 'delivered'].includes(value?.toLowerCase() ?? '') ? 'bg-green-100 text-green-700' : ['cancelled'].includes(value?.toLowerCase() ?? '') ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'
</script>

<template>
    <div class="w-full space-y-6 px-4"><div class="flex flex-wrap items-start justify-between gap-4"><div><Link href="/shipments" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"><ArrowLeft class="h-4 w-4" /> Back to Shipments</Link><h1 class="mt-4 text-2xl font-semibold">Shipment {{ shipment.shipment_number }}</h1><p class="text-sm text-muted-foreground">Shipment details, milestones, and status history.</p></div><Button as-child><Link :href="`/shipments/${shipment.id}/edit`"><Pencil class="mr-2 h-4 w-4" /> Edit Shipment</Link></Button></div>
        <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Shipment Information</h2></div><dl class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3"><div><dt>Shipment Number</dt><dd>{{ shipment.shipment_number || '-' }}</dd></div><div><dt>BL Number</dt><dd>{{ shipment.bl_number || '-' }}</dd></div><div><dt>Vessel Name</dt><dd>{{ shipment.vessel_name || '-' }}</dd></div><div><dt>Voyage Number</dt><dd>{{ shipment.voyage_number || '-' }}</dd></div><div><dt>Status Document</dt><dd><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass(shipment.status_document)">{{ label(shipment.status_document) }}</span></dd></div><div><dt>Tracking Status</dt><dd>{{ label(shipment.tracking_status) }}</dd></div><div><dt>Forwarder</dt><dd>{{ shipment.forwarder?.forwarder_name || '-' }}</dd></div><div><dt>Shipping Line</dt><dd>{{ shipment.shipping_line?.shipping_line_name || '-' }}</dd></div><div><dt>Port of Loading</dt><dd>{{ shipment.port_of_loading?.port_name || '-' }}</dd></div><div><dt>Port of Discharge</dt><dd>{{ shipment.port_of_discharge?.port_name || '-' }}</dd></div><div><dt>Warehouse</dt><dd>{{ shipment.warehouse?.warehouse_name || '-' }}</dd></div></dl></section>
        <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Shipment Schedule</h2></div><dl class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-4"><div><dt>ETD</dt><dd>{{ dateLabel(shipment.etd) }}</dd></div><div><dt>ETA</dt><dd>{{ dateLabel(shipment.eta) }}</dd></div><div><dt>ATD</dt><dd>{{ dateLabel(shipment.atd) }}</dd></div><div><dt>ATA</dt><dd>{{ dateLabel(shipment.ata) }}</dd></div></dl></section>
        <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Milestones</h2></div><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-4">Milestone</th><th class="p-4">Planned</th><th class="p-4">Actual</th><th class="p-4">Status</th><th class="p-4">Remarks</th></tr></thead><tbody><tr v-for="milestone in shipment.milestones ?? []" :key="milestone.id" class="border-b"><td class="p-4 font-medium">{{ milestone.milestone_name }}</td><td class="p-4">{{ dateLabel(milestone.planned_date) }}</td><td class="p-4">{{ dateLabel(milestone.actual_date ?? milestone.milestone_date) }}</td><td class="p-4">{{ milestone.status }}</td><td class="p-4">{{ milestone.remarks || '-' }}</td></tr><tr v-if="!shipment.milestones?.length"><td colspan="5" class="p-6 text-center text-muted-foreground">No milestones recorded.</td></tr></tbody></table></div></section>
        <section class="rounded-lg border bg-card"><div class="border-b p-6"><h2 class="font-semibold">Status History</h2></div><div class="divide-y"><div v-for="history in shipment.status_histories ?? []" :key="history.id" class="grid gap-2 p-6 md:grid-cols-[180px_1fr]"><div class="text-sm text-muted-foreground">{{ dateTimeLabel(history.status_date) }}</div><div><p class="font-medium">{{ label(history.status) }}</p><p class="text-sm text-muted-foreground">{{ [history.location, history.country].filter(Boolean).join(', ') || 'Location not recorded' }}</p><p v-if="history.remarks" class="mt-1 text-sm">{{ history.remarks }}</p></div></div><p v-if="!shipment.status_histories?.length" class="p-6 text-center text-muted-foreground">No status history recorded.</p></div></section>
    </div>
</template>

<style scoped>
dt { color: hsl(var(--muted-foreground)); font-size: 0.875rem; }
dd { margin-top: 0.25rem; font-weight: 500; }
</style>
