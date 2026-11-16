<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    edit,
    index,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';
import AllocatedPurchaseOrderItems from '@/components/shipment/AllocatedPurchaseOrderItems.vue';
import ShipmentStatusHistory from '@/components/shipment/ShipmentStatusHistory.vue';

interface StatusHistory {
    id: number;
    status_type: 'document' | 'shipment';
    status: string;
    status_date: string;
}

interface LocalShipmentItem {
    id: number;
    quantity: number;
    purchase_order_item: {
        product_code: string;
        product_name: string;
        unit: string | null;
    };
}

interface LocalShipment {
    id: number;
    shipment_number: string;
    delivery_order_number: string | null;
    etd: string | null;
    atd: string | null;
    status_document: string | null;
    tracking_status: string | null;
    remarks: string | null;
    purchase_orders: Array<{
        po_number: string;
        supplier?: { supplier_name: string } | null;
    }>;
    purchase_order?: {
        po_number: string;
        supplier?: { supplier_name: string } | null;
    } | null;
    warehouse?: { warehouse_name: string } | null;
    status_histories: StatusHistory[];
    items: LocalShipmentItem[];
}
interface AllocatedItem {
    id: number | string;
    product_code: string | null;
    product_name: string | null;
    quantity: number | string;
    unit: string | null;
    container_number?: string | null;
}

const { localShipment } = defineProps<{
    localShipment: LocalShipment;
}>();

const allocatedItems = computed<AllocatedItem[]>(() =>
    (localShipment.items ?? []).map((item) => ({
        id: item.id,
        product_code: item.purchase_order_item?.product_code ?? null,
        product_name: item.purchase_order_item?.product_name ?? null,
        quantity: item.quantity,
        unit: item.purchase_order_item?.unit ?? null,
    })),
);

function dateLabel(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

</script>

<template>
    <Head :title="localShipment.shipment_number" />

    <main class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-2">
                <Link
                    :href="index.url()"
                    class="text-sm text-muted-foreground hover:text-foreground"
                    >Kembali ke local shipment</Link
                >
                <h1 class="text-2xl font-semibold">
                    {{ localShipment.shipment_number }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    Detail pengiriman lokal dan riwayat status.
                </p>
            </div>
            <Link
                :href="edit.url(localShipment.id)"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
                >Edit shipment</Link
            >
        </header>

        <section class="rounded-xl border bg-card p-5">
            <h2 class="font-semibold">Informasi Pengiriman</h2>
            <dl class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs text-muted-foreground">
                        Purchase order
                    </dt>
                    <dd class="mt-1 font-medium">
                        {{ localShipment.purchase_orders.map((purchaseOrder) => purchaseOrder.po_number).join(', ') || localShipment.purchase_order?.po_number || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">Vendor</dt>
                    <dd class="mt-1 font-medium">
                        {{
                            localShipment.purchase_orders.map((purchaseOrder) => purchaseOrder.supplier?.supplier_name).filter(Boolean).join(', ') || localShipment.purchase_order?.supplier?.supplier_name || '-'
                        }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">Gudang</dt>
                    <dd class="mt-1 font-medium">
                        {{ localShipment.warehouse?.warehouse_name || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">
                        Nomor delivery order
                    </dt>
                    <dd class="mt-1 font-medium">
                        {{ localShipment.delivery_order_number || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">
                        Status dokumen
                    </dt>
                    <dd class="mt-1 font-medium">
                        {{ localShipment.status_document || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">
                        Status tracking
                    </dt>
                    <dd class="mt-1 font-medium">
                        {{ localShipment.tracking_status || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">ETD</dt>
                    <dd class="mt-1 font-medium">
                        {{ dateLabel(localShipment.etd) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">ATD</dt>
                    <dd class="mt-1 font-medium">
                        {{ dateLabel(localShipment.atd) }}
                    </dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-xs text-muted-foreground">Catatan</dt>
                    <dd class="mt-1 font-medium whitespace-pre-line">
                        {{ localShipment.remarks || '-' }}
                    </dd>
                </div>
            </dl>
        </section>

        <AllocatedPurchaseOrderItems
            :items="allocatedItems"
            description="Purchase order items allocated to this shipment"
        />

        <ShipmentStatusHistory
            :histories="localShipment.status_histories"
            description="Riwayat status dokumen dan shipment, berurutan berdasarkan waktu."
            empty-message="Belum ada riwayat status."
        />
    </main>
</template>
