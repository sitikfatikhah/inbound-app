<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'

interface Related { id: number | string;[key: string]: string | number }
interface Shipment {
    id: number | string
    shipment_number: string | null
    forwarder?: Related | null
    shipping_line?: Related | null
    port_of_loading?: Related | null
    port_of_discharge?: Related | null
    warehouse?: Related | null
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
const props = defineProps<{ shipments?: Shipment[] }>()
const search = ref('')
const filteredShipments = computed(() => {
    const keyword = search.value.trim().toLowerCase()
    if (!keyword) return props.shipments ?? []
    return (props.shipments ?? []).filter((shipment) => [shipment.shipment_number, shipment.bl_number, shipment.vessel_name, shipment.voyage_number, shipment.forwarder?.forwarder_name, shipment.shipping_line?.shipping_line_name, shipment.status_document, shipment.tracking_status].some((value) => String(value ?? '').toLowerCase().includes(keyword)))
})
const dateLabel = (value: string | null) => value?.slice(0, 10) ?? '-'
const label = (value: string | null) => value ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()) : '-'
const statusClass = (value: string | null) => { const status = value?.toLowerCase(); if (['completed', 'delivered', 'customs cleared'].includes(status ?? '')) return 'bg-green-100 text-green-700'; if (['in transit', 'booked'].includes(status ?? '')) return 'bg-blue-100 text-blue-700'; if (['cancelled'].includes(status ?? '')) return 'bg-red-100 text-red-700'; return 'bg-gray-100 text-gray-700' }
</script>

<template>
    <div class="w-full space-y-6 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Shipments</h1>
                <p class="text-sm text-muted-foreground">Manage shipments, milestones, and status history.</p>
            </div><Button as-child>
                <Link href="/shipments/create">Add Shipment</Link>
            </Button>
        </div>
        <Input v-model="search" type="search" placeholder="Search shipments..." class="max-w-sm" />
        <div class="overflow-x-auto rounded-md border">
            <Table>
                <TableCaption>A list of your shipments.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Shipment Number</TableHead>
                        <TableHead>Forwarder</TableHead>
                        <TableHead>Shipping Line</TableHead>
                        <TableHead>POL</TableHead>
                        <TableHead>POD</TableHead>
                        <TableHead>Warehouse</TableHead>
                        <TableHead>BL Number</TableHead>
                        <TableHead>Vessel</TableHead>
                        <TableHead>Voyage</TableHead>
                        <TableHead>ETD</TableHead>
                        <TableHead>ETA</TableHead>
                        <TableHead>ATD</TableHead>
                        <TableHead>ATA</TableHead>
                        <TableHead>Status Document</TableHead>
                        <TableHead>Tracking Status</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="filteredShipments.length">
                        <TableRow v-for="shipment in filteredShipments" :key="shipment.id">
                            <TableCell class="font-medium">{{ shipment.shipment_number || '-' }}</TableCell>
                            <TableCell>{{ shipment.forwarder?.forwarder_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.shipping_line?.shipping_line_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.port_of_loading?.port_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.port_of_discharge?.port_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.warehouse?.warehouse_name || '-' }}</TableCell>
                            <TableCell>{{ shipment.bl_number || '-' }}</TableCell>
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
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child><Button variant="ghost" size="icon">⋮</Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/shipments/${shipment.id}`">View</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/shipments/${shipment.id}/edit`">Edit</Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="16" class="h-32 text-center">{{ search.trim() ? 'No shipments match your search criteria.' : 'No shipments found.' }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <div class="text-sm text-muted-foreground">Showing {{ filteredShipments.length }} shipments.</div>
    </div>
</template>
