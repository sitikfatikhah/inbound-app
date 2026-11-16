<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Pencil } from 'lucide-vue-next'
import { computed } from 'vue'
import AllocatedPurchaseOrderItems from '@/components/shipment/AllocatedPurchaseOrderItems.vue'
import ShipmentMilestonesTable from '@/components/shipment/ShipmentMilestonesTable.vue'
import ShipmentStatusHistory from '@/components/shipment/ShipmentStatusHistory.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

interface Related { id: number | string;[key: string]: string | number }
interface PurchaseOrder { id: number | string; po_number: string }
interface Milestone { id: number | string; milestone_name: string; milestone_date: string | null; planned_date: string | null; actual_date: string | null; status: string; remarks: string | null }
interface History { 
    id: number | string; 
    status: string | null; 
    status_date: string | null; 
    location: string | null; 
    country: string | null; 
    remarks: string | null }
interface ShipmentContainer {
    id: number | string;
    container_number: string | null;
    product_code: string | null;
    quantity: number | string;
    purchase_order_item?: {
        product_code: string | null;
        product_name: string | null;
        unit: string | null;
    } | null;
}
interface Shipment {
    id: number | string; 
    shipment_number: string | null; 
    forwarder?: Related | null; 
    shipping_line?: Related | null; 
    purchase_orders?: PurchaseOrder[];
    port_of_loading?: Related | null; 
    port_of_discharge?: Related | null; 
    warehouse?: Related | null; 
    bl_number: string | null; 
    invoice_number: string | null;
    vessel_name: string | null; 
    voyage_number: string | null; 
    etd: string | null; 
    eta: string | null; 
    atd: string | null; 
    ata: string | null; 
    status_document: string | null; 
    tracking_status: string | null; 
    milestones?: Milestone[]; 
    status_histories?: History[];
    shipment_containers?: ShipmentContainer[];
}
const props = defineProps<{ shipment: Shipment }>()
const allocatedItems = computed(() =>
    (props.shipment.shipment_containers ?? []).map((container) => ({
        id: container.id,
        product_code:
            container.purchase_order_item?.product_code ??
            container.product_code,
        product_name: container.purchase_order_item?.product_name ?? null,
        quantity: container.quantity,
        unit: container.purchase_order_item?.unit ?? null,
        container_number: container.container_number,
    })),
)
const dateLabel = (value: string | null) => value?.slice(0, 10) ?? '-'
const label = (value: string | null) => value ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()) : '-'
</script>

<template>
    <div class="mx-4 w-full space-y-6 rounded-lg border bg-card p-6 sm:mx-0 sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <Link href="/shipments"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" /> Back to Shipments
                </Link>
                <h1 class="mt-4 text-2xl font-semibold">Shipment {{ shipment.shipment_number }}</h1>
                <p class="text-sm text-muted-foreground">Shipment details, milestones, and status history.</p>
            </div><Button as-child>
                <Link :href="`/shipments/${shipment.id}/edit`">
                    <Pencil class="mr-2 h-4 w-4" /> Edit Shipment
                </Link>
            </Button>
        </div>
        <section class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">Shipment Information</h2>
            </div>
            <dl class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt>Shipment Number</dt>
                    <dd>{{ shipment.shipment_number || '-' }}</dd>
                </div>
                <div>
                    <dt>Purchase Orders</dt>
                    <dd>{{ (shipment.purchase_orders ?? []).map((purchaseOrder) => purchaseOrder.po_number).join(', ') || '-' }}</dd>
                </div>
                <div>
                    <dt>BL Number</dt>
                    <dd>{{ shipment.bl_number || '-' }}</dd>
                </div>
                <div>
                    <dt>Invoice Number</dt>
                    <dd>{{ shipment.invoice_number || '-' }}</dd>
                </div>
                <div>
                    <dt>Vessel Name</dt>
                    <dd>{{ shipment.vessel_name || '-' }}</dd>
                </div>
                <div>
                    <dt>Voyage Number</dt>
                    <dd>{{ shipment.voyage_number || '-' }}</dd>
                </div>
                <div>
                    <dt>Status Document</dt>
                    <dd>
                        <Badge variant="outline">
                            {{ label(shipment.status_document) }}
                        </Badge>
                    </dd>
                </div>
                <div>
                    <dt>Tracking Status</dt>
                    <dd>{{ label(shipment.tracking_status) }}</dd>
                </div>
                <div>
                    <dt>Forwarder</dt>
                    <dd>{{ shipment.forwarder?.forwarder_name || '-' }}</dd>
                </div>
                <div>
                    <dt>Shipping Line</dt>
                    <dd>{{ shipment.shipping_line?.shipping_line_name || '-' }}</dd>
                </div>
                <div>
                    <dt>Port of Loading</dt>
                    <dd>{{ shipment.port_of_loading?.port_name || '-' }}</dd>
                </div>
                <div>
                    <dt>Port of Discharge</dt>
                    <dd>{{ shipment.port_of_discharge?.port_name || '-' }}</dd>
                </div>
                <div>
                    <dt>Warehouse</dt>
                    <dd>{{ shipment.warehouse?.warehouse_name || '-' }}</dd>
                </div>
            </dl>
        </section>
        <section class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">Shipment Schedule</h2>
            </div>
            <dl class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt>ETD</dt>
                    <dd>{{ dateLabel(shipment.etd) }}</dd>
                </div>
                <div>
                    <dt>ETA</dt>
                    <dd>{{ dateLabel(shipment.eta) }}</dd>
                </div>
                <div>
                    <dt>ATD</dt>
                    <dd>{{ dateLabel(shipment.atd) }}</dd>
                </div>
                <div>
                    <dt>ATA</dt>
                    <dd>{{ dateLabel(shipment.ata) }}</dd>
                </div>
            </dl>
        </section>
        
        <AllocatedPurchaseOrderItems
            :items="allocatedItems"
            description="Items allocated to this shipment, grouped by container."
        />

        <ShipmentMilestonesTable :milestones="shipment.milestones ?? []" />

        <ShipmentStatusHistory :histories="shipment.status_histories ?? []" />

    </div>
</template>

<style scoped>
dt {
    color: hsl(var(--muted-foreground));
    font-size: 0.875rem;
}

dd {
    margin-top: 0.25rem;
    font-weight: 500;
}
</style>
