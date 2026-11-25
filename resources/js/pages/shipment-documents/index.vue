<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { create, edit as editShipment, show as showShipment } from '@/actions/App/Http/Controllers/ShipmentController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'

interface Related { id: number | string;[key: string]: string | number }
interface PurchaseOrder { id: number | string; po_number: string }
interface Shipment {
    id: number | string
    shipment_number: string | null
    forwarder?: Related | null
    purchase_orders?: PurchaseOrder[]
    shipping_line?: Related | null
    port_of_loading?: Related | null
    port_of_discharge?: Related | null
    warehouse?: Related | null
    bl_number: string | null
    invoice_number: string | null
    vessel_name: string | null
    voyage_number: string | null
    etd: string | null
    eta: string | null
    atd: string | null
    ata: string | null
    status_document: string | null
    tracking_status: string | null
}
interface shipmentDocument {
    document_type: string
    document_name: string
    document_number: string
    document_date: Date
    document_file: string
    verification_status: string
}
interface Insights {
    document_completeness_percentage: number
    average_etd_revision_frequency: number
    average_eta_revision_frequency: number
    average_schedule_deviation_days: number
    most_common_delay_reason: { reason: string; count: number } | null
}
interface Notification {
    type: 'critical' | 'warning' | 'complete'
    shipment_id: number
    shipment_number: string
    document_name:string
    message: string
}
const props = defineProps<{
    shipments?: Shipment[]
    insights?: Insights
    shipmentDocument: shipmentDocument
    notifications?: Notification[]
}>()
const search = ref('')
const filteredShipments = computed(() => {
    const keyword = search.value.trim().toLowerCase()
    if (!keyword) return props.shipments ?? []
    return (props.shipments ?? []).filter((shipment) => [shipment.shipment_number, shipment.bl_number, shipment.vessel_name, shipment.voyage_number, shipment.forwarder?.forwarder_name, shipment.shipping_line?.shipping_line_name, shipment.status_document, shipment.tracking_status, ...(shipment.purchase_orders ?? []).map((purchaseOrder) => purchaseOrder.po_number)].some((value) => String(value ?? '').toLowerCase().includes(keyword)))
})
const dateLabel = (value: string | Date | null) => value instanceof Date ? value.toISOString().slice(0, 10) : value?.slice(0, 10) ?? '-'
const label = (value: string | null) => value ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()) : '-'
const statusClass = (value: string | null) => { const status = value?.toLowerCase(); if (['completed', 'delivered', 'customs cleared'].includes(status ?? '')) return 'bg-green-100 text-green-700'; if (['in transit', 'booked'].includes(status ?? '')) return 'bg-blue-100 text-blue-700'; if (['cancelled'].includes(status ?? '')) return 'bg-red-100 text-red-700'; return 'bg-gray-100 text-gray-700' }
</script>

<template>
    <div class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Shipments</h1>
                <p class="text-sm text-muted-foreground">Inbound tracking, documents, customs clearance, and schedule performance.</p>
            </div>
            <Button as-child>
                <Link :href="create.url()">Add Shipment</Link>
            </Button>
        </div>
        <section v-if="props.insights" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          
            <article class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Document completeness</p>
                <p class="mt-2 text-2xl font-semibold">{{ props.insights.document_completeness_percentage }}%</p>
            </article>
            <article class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Average ETA revisions per shipment</p>
                <p class="mt-2 text-2xl font-semibold">{{ props.insights.average_etd_revision_frequency }}</p>
            </article>
            <article class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Average ETA revisions per shipment</p>
                <p class="mt-2 text-2xl font-semibold">{{ props.insights.average_eta_revision_frequency }}</p>
            </article>
            <article class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Average schedule deviation</p>
                <p class="mt-2 text-2xl font-semibold">{{ props.insights.average_schedule_deviation_days }} <span class="text-sm font-normal">days</span></p>
            </article>
            <article class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Most common delay reason</p>
                <p class="mt-2 font-semibold">{{ props.insights.most_common_delay_reason?.reason ?? 'No delay reasons recorded' }}</p>
                <p v-if="props.insights.most_common_delay_reason" class="text-xs text-muted-foreground">{{ props.insights.most_common_delay_reason.count }} recorded revision(s)</p>
            </article>
        </section>
        <section class="space-y-3" aria-labelledby="shipment-alerts-heading">
            <div class="flex items-center justify-between">
                <h2 id="shipment-alerts-heading" class="text-lg font-semibold">Shipment notifications</h2>
                <span class="text-sm text-muted-foreground">{{ props.notifications?.length ?? 0 }} notifications</span>
            </div>
            <p v-if="!props.notifications?.length" class="rounded-lg border p-4 text-sm text-muted-foreground">No shipment notifications.</p>
            <Link
                v-for="notification in props.notifications ?? []"
                :key="`${notification.type}-${notification.shipment_id}-${notification.message}`"
                :href="showShipment.url(notification.shipment_id)"
                class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                :class="notification.type === 'critical' ? 'border-destructive/50 bg-destructive/5' : notification.type === 'warning' ? 'border-amber-500/40 bg-amber-500/5' : 'border-green-600/30 bg-green-500/5'"
            >
                <span>
                    <strong class="mr-2 uppercase">{{ notification.type }}</strong>
                    {{ notification.message }}
                </span>
                <span class="text-sm font-medium">{{ notification.shipment_number }}</span>
            </Link>
        </section>
        <Input v-model="search" type="search" placeholder="Search shipments..." class="max-w-sm" />
        <div class="overflow-x-auto rounded-md border">
            <Table>
                <TableCaption>A list of your shipments.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">No</TableHead>
                        <TableHead>Shipment Number</TableHead>
                        <TableHead>Purchase Orders</TableHead>
                        <TableHead>Forwarder</TableHead>
                        <TableHead>Shipping Line</TableHead>
                        <TableHead>POL</TableHead>
                        <TableHead>POD</TableHead>
                        <TableHead>Warehouse</TableHead>
                        <TableHead>BL Number</TableHead>
                        <TableHead>Invoice Number</TableHead>
                        <TableHead>Vessel</TableHead>
                        <TableHead>Voyage</TableHead>
                        <TableHead>ETD</TableHead>
                        <TableHead>ETA</TableHead>
                        <TableHead>ATD</TableHead>
                        <TableHead>ATA</TableHead>
                        <TableHead>Status Document</TableHead>
                        <TableHead>Tracking Status</TableHead>
                        <TableHead>document_type</TableHead>
                        <TableHead>document_name</TableHead>
                        <TableHead>document_number</TableHead>
                        <TableHead>document_date</TableHead>
                        <TableHead>document_file</TableHead>
                        <TableHead>verification_status</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="filteredShipments.length">
                        <TableRow v-for="shipment in filteredShipments" :key="shipment.id">
                            <TableCell class="font-medium" >{{ shipment.id }}</TableCell>
                            <TableCell class="font-medium">{{ shipment.shipment_number || '-' }}</TableCell>
                            <TableCell>{{ (shipment.purchase_orders ?? []).map((purchaseOrder) => purchaseOrder.po_number).join(', ') || '-' }}</TableCell>
                            <TableCell>{{ shipment.forwarder?.forwarder_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.shipping_line?.shipping_line_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.port_of_loading?.port_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.port_of_discharge?.port_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.warehouse?.warehouse_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.bl_number || '-' }}</TableCell>
                            <TableCell>{{ shipment.invoice_number || '-' }}</TableCell>
                            <TableCell>{{ shipment.vessel_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.voyage_number || '-' }}</TableCell>
                            <TableCell>{{ dateLabel(shipment.etd) }}</TableCell>
                            <TableCell>{{ dateLabel(shipment.eta) }}</TableCell>
                            <TableCell>{{ dateLabel(shipment.atd) }}</TableCell>
                            <TableCell>{{ dateLabel(shipment.ata) }}</TableCell>
                            <TableCell><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="statusClass(shipment.status_document)">{{ label(shipment.status_document)
                                    }}</span></TableCell>
                            <TableCell>{{ label(shipment.tracking_status) }}</TableCell>
                            <TableCell>{{ shipmentDocument.document_type || '-' }}</TableCell>
                            <TableCell>{{ shipmentDocument.document_name || '-' }}</TableCell>
                            <TableCell>{{ dateLabel(shipmentDocument.document_date) || '-' }}</TableCell>
                            <TableCell>{{ shipmentDocument.document_file || '-' }}</TableCell>
                            <TableCell>{{ shipmentDocument.verification_status || '-' }}</TableCell>

                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child><Button variant="ghost" size="icon">⋮</Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem as-child>
                                            <Link :href="showShipment.url(Number(shipment.id))">View</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem as-child>
                                            <Link :href="editShipment.url(Number(shipment.id))">Edit</Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="19" class="h-32 text-center">{{ search.trim() ? 'No shipments match your search criteria.' : 'No shipments found.' }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <div class="text-sm text-muted-foreground">Showing {{ filteredShipments.length }} shipments.</div>
    </div>
</template>
