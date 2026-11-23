<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Pencil } from 'lucide-vue-next'
import { computed } from 'vue'
import { edit as editShipment, index as shipmentsIndex } from '@/actions/App/Http/Controllers/ShipmentController'
import AllocatedPurchaseOrderItems from '@/components/shipment/AllocatedPurchaseOrderItems.vue'
import ShipmentMilestonesTable from '@/components/shipment/ShipmentMilestonesTable.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import ShipmentActivityLog from '@/pages/shipment/components/ShipmentActivityLog.vue'
import ShipmentCustomsStatus from '@/pages/shipment/components/ShipmentCustomsStatus.vue'
import ShipmentDocumentChecklist from '@/pages/shipment/components/ShipmentDocumentChecklist.vue'
import ShipmentOverview from '@/pages/shipment/components/ShipmentOverview.vue'
import ShipmentScheduleHistory from '@/pages/shipment/components/ShipmentScheduleHistory.vue'
import ShipmentTrackingTimeline from '@/pages/shipment/components/ShipmentTrackingTimeline.vue'
import UpdateTrackingStatus from '@/pages/shipment/forms/UpdateTrackingStatus.vue'

interface Related { id: number | string;[key: string]: string | number }
interface PurchaseOrder { id: number | string; po_number: string }
interface Milestone { id: number | string; milestone_name: string; milestone_date: string | null; planned_date: string | null; actual_date: string | null; status: string; remarks: string | null }
interface ShipmentStatusHistory {
    id: number
    status: string | null
    status_date: string | null
    location: string | null
    country: string | null
    remarks: string | null
}
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
interface ShipmentDocument {
    id: number
    document_type: string
    document_name: string
    document_file: string | null
    document_url: string | null
    document_number: string | null
    document_date: string | null
    verification_status: 'missing' | 'pending' | 'complete' | 'deficient'
    verification_notes: string | null
    updated_at: string
}

interface ScheduleRevision {
    id: number
    schedule_type: 'eta' | 'etd'
    previous_date: string | null
    new_date: string
    revision_reason: string
    created_at: string
}
interface CustomsClearance {
    // jalur: 'Hijau' | 'Merah' | null
    // pib_number: string | null
    // pib_date: string | null
    // sppb_number: string | null
    // sppb_date: string | null
    // inspection_start: string | null
    // inspection_end: string | null
    // inspection_duration: number | null
    // remarks: string | null
    // updated_at: string
}
interface Shipment {
    id: number | string
    shipment_number: string | null
    forwarder?: Related | null
    shipping_line?: Related | null
    purchase_orders?: PurchaseOrder[];
    port_of_loading?: Related | null
    port_of_discharge?: Related | null
    warehouse?: Related | null
    bl_number: string | null; 
    invoice_number: string | null;
    vessel_name: string | null
    voyage_number: string | null
    etd: string | null
    eta: string | null
    original_etd: string | null;
    original_eta: string | null;
    atd: string | null
    ata: string | null
    status_document: string | null
    tracking_status: string | null
    milestones?: Milestone[]; 
    status_histories?: ShipmentStatusHistory[];
    shipment_containers?: ShipmentContainer[];
    shipment_documents?: ShipmentDocument[];
    customs_clearance?: CustomsClearance | null;
    schedule_histories?: ScheduleRevision[];
}
const props = defineProps<{
    shipment: Shipment
    documentTypes: Record<string, string>
    requiredDocumentTypes: string[]
    inspectionSlaHours: number
 }>()
const documentCompleteness = computed(() => {
    const documents = props.shipment.shipment_documents ?? []
    const completeCount = props.requiredDocumentTypes.filter((type) =>
        documents.some((document) => document.document_type === type && document.verification_status === 'complete'),
    ).length

    return props.requiredDocumentTypes.length
        ? Math.round((completeCount / props.requiredDocumentTypes.length) * 100)
        : 0
})
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
const milestones = computed(() =>
    (props.shipment.milestones ?? []).map((milestone) => ({
        ...milestone,
        id: Number(milestone.id),
    })),
)
const dateLabel = (value: string | null) => value?.slice(0, 10) ?? '-'
const label = (value: string | null) => value ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()) : '-'
</script>

<template>
    <div class="mx-4 w-full space-y-6 rounded-lg border bg-card p-6 sm:mx-0 sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <Link :href="shipmentsIndex.url()"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" /> Back to Shipments
                </Link>
                <h1 class="mt-4 text-2xl font-semibold">Shipment {{ shipment.shipment_number }}</h1>
                <p class="text-sm text-muted-foreground">Shipment details, milestones, and status history.</p>
            </div><Button as-child>
                <Link :href="editShipment.url(Number(shipment.id))">
                    <Pencil class="mr-2 h-4 w-4" /> Edit Shipment
                </Link>
            </Button>
        </div>
        <ShipmentOverview
            :shipment="shipment"
            :document-completeness="documentCompleteness"
        />
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
        <ShipmentDocumentChecklist
            :shipment-id="Number(shipment.id)"
            :documents="shipment.shipment_documents ?? []"
            :document-types="documentTypes"
            :required-document-types="requiredDocumentTypes"
        />
        <ShipmentCustomsStatus
            :shipment-id="Number(shipment.id)"
            :clearance="shipment.customs_clearance ?? null"
            :inspection-sla-hours="inspectionSlaHours"
        />
        <ShipmentScheduleHistory
            :shipment-id="Number(shipment.id)"
            :eta="shipment.eta"
            :etd="shipment.etd"
            :original-eta="shipment.original_eta"
            :original-etd="shipment.original_etd"
            :histories="shipment.schedule_histories ?? []"
        />
        <ShipmentMilestonesTable :milestones="milestones" />
        <UpdateTrackingStatus
            :shipment-id="Number(shipment.id)"
            :tracking-status="shipment.tracking_status"
        />
        <ShipmentTrackingTimeline
            :histories="shipment.status_histories ?? []"
            :milestones="milestones"
        />
        <ShipmentActivityLog
            :status-histories="shipment.status_histories ?? []"
            :schedule-histories="shipment.schedule_histories ?? []"
            :documents="shipment.shipment_documents ?? []"
            :customs-clearance="shipment.customs_clearance ?? null"
        />
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
