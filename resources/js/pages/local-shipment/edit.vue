<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PlusCircle, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    index,
    update,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface PurchaseOrder {
    id: number;
    po_number: string;
    supplier?: {
        supplier_name: string;
    };
}

interface PurchaseOrderItem {
    id: number;
    purchase_order_id: number;
    product_id: number;
    product_code: string | null;
    product_name: string | null;
    quantity: number | string | null;
    remaining_qty: number | string | null;
    unit: string | null;
}

interface ShipmentItemRow {
    purchase_order_id: number | null;
    purchase_order_item_id: number | null;
    product_code: string;
    product_name: string;
    product_id: number | null;
    quantity: number | null;
    unit: string;
}

interface Warehouse {
    id: number;
    warehouse_name: string;
}

interface TrackingStatus {
    id: number;
    category: string;
    name: string;
    is_active: boolean;
}

interface LocalShipment {
    id: number;
    shipment_number: string;
    warehouse_id: number;
    delivery_order_number: string | null;
    etd: string | null;
    atd: string | null;
    status_document: string | null;
    tracking_status: string | null;
    remarks: string | null;
    purchase_order?: {
        po_number: string;
        supplier?: { supplier_name: string } | null;
    } | null;
}

const props = defineProps<{
    localShipment: LocalShipment;
    purchaseOrders: PurchaseOrder[];
    purchaseOrderItems: PurchaseOrderItem[];
    items: ShipmentItemRow[];
    warehouses: Warehouse[];
    status_document: TrackingStatus[];
    tracking_status: TrackingStatus[];
}>();

const form = useForm({
    shipment_number: props.localShipment.shipment_number,
    warehouse_id: String(props.localShipment.warehouse_id),
    delivery_order_number: props.localShipment.delivery_order_number ?? '',
    etd: props.localShipment.etd ?? '',
    atd: props.localShipment.atd ?? '',
    status_document: props.localShipment.status_document ?? '',
    tracking_status: props.localShipment.tracking_status ?? '',
    remarks: props.localShipment.remarks ?? '',
    items: props.items.map((item) => ({ ...item })),
});

const selectedPos = computed(
    () =>
        new Set(
            form.items
                .map((item) => item.purchase_order_id)
                .filter((purchaseOrderId): purchaseOrderId is number =>
                    Boolean(purchaseOrderId),
                ),
        ),
);
const getRemainingQuantity = (item: PurchaseOrderItem): number =>
    Math.max(0, Number(item.remaining_qty ?? item.quantity ?? 0));
const getPurchaseOrderItems = (purchaseOrderId: number | null): PurchaseOrderItem[] => {
    if (!purchaseOrderId) {
        return [];
    }

    return props.purchaseOrderItems.filter(
        (item) =>
            Number(item.purchase_order_id) === Number(purchaseOrderId) &&
            getRemainingQuantity(item) > 0,
    );
};
const availablePurchaseOrders = computed(() =>
    props.purchaseOrders.filter(
        (purchaseOrder) => getPurchaseOrderItems(purchaseOrder.id).length > 0,
    ),
);
const getSelectedItem = (row: ShipmentItemRow) =>
    getPurchaseOrderItems(row.purchase_order_id).find(
        (item) => item.id === row.purchase_order_item_id,
    );
const getSelectedRemainingQuantity = (row: ShipmentItemRow): number | undefined => {
    const item = getSelectedItem(row);

    return item ? getRemainingQuantity(item) : undefined;
};
const addPurchaseOrderItem = (): void => {
    form.items.push({
        purchase_order_id: null,
        purchase_order_item_id: null,
        product_code: '',
        product_name: '',
        product_id: null,
        quantity: null,
        unit: '',
    });
};
const selectPurchaseOrderItem = (row: ShipmentItemRow): void => {
    const selectedItem = getSelectedItem(row);

    row.quantity = null;
    row.product_id = selectedItem?.product_id ?? null;
    row.product_code = selectedItem?.product_code ?? '';
    row.product_name = selectedItem?.product_name ?? '';
    row.unit = selectedItem?.unit ?? '';
};
const updateQuantity = (row: ShipmentItemRow, value: string | number): void => {
    row.quantity = value === '' ? null : Number(value);
};

function submit() {
    form.put(update.url(props.localShipment.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit ${localShipment.shipment_number}`" />

    <main class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <header class="space-y-2">
            <Link
                :href="index.url()"
                class="text-sm text-muted-foreground hover:text-foreground"
            >
                Kembali ke local shipment
            </Link>
            <h1 class="text-2xl font-semibold">Edit Local Shipment</h1>
            <p class="text-sm text-muted-foreground">
                {{ localShipment.shipment_number }}
            </p>
        </header>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="space-y-5 rounded-xl border bg-card p-5">
                <h2 class="font-semibold">Informasi Pengiriman</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2 text-sm font-medium">
                        Nomor shipment
                        <input
                            v-model="form.shipment_number"
                            readonly
                            class="h-10 w-full rounded-md border bg-muted px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.shipment_number"
                            class="block text-xs text-destructive"
                            >{{ form.errors.shipment_number }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Purchase order utama
                        <input
                            :value="localShipment.purchase_order?.po_number ?? '-'"
                            disabled
                            class="h-10 w-full rounded-md border bg-muted px-3 font-normal"
                        />
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Gudang
                        <select
                            v-model="form.warehouse_id"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        >
                            <option value="" disabled>Pilih gudang</option>
                            <option
                                v-for="warehouse in warehouses"
                                :key="warehouse.id"
                                :value="String(warehouse.id)"
                            >
                                {{ warehouse.warehouse_name }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.warehouse_id"
                            class="block text-xs text-destructive"
                            >{{ form.errors.warehouse_id }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Nomor delivery order
                        <input
                            v-model="form.delivery_order_number"
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.delivery_order_number"
                            class="block text-xs text-destructive"
                            >{{ form.errors.delivery_order_number }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        ETD
                        <input
                            v-model="form.etd"
                            type="date"
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.etd"
                            class="block text-xs text-destructive"
                            >{{ form.errors.etd }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        ATD
                        <input
                            v-model="form.atd"
                            type="date"
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.atd"
                            class="block text-xs text-destructive"
                            >{{ form.errors.atd }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Status dokumen
                        <select
                            v-model="form.status_document"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        >
                            <option value="" disabled>Pilih status dokumen</option>
                            <option
                                v-for="status in status_document"
                                :key="status.id"
                                :value="status.name"
                            >
                                {{ status.category }} - {{ status.name }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.status_document"
                            class="block text-xs text-destructive"
                            >{{ form.errors.status_document }}</span
                        >
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Status tracking
                        <select
                            v-model="form.tracking_status"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        >
                            <option value="" disabled>Pilih status tracking</option>
                            <option
                                v-for="status in tracking_status"
                                :key="status.id"
                                :value="status.name"
                            >
                                {{ status.category }} - {{ status.name }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.tracking_status"
                            class="block text-xs text-destructive"
                            >{{ form.errors.tracking_status }}</span
                        >
                    </label>
                </div>
                <label class="block space-y-2 text-sm font-medium">
                    Catatan
                    <textarea
                        v-model="form.remarks"
                        rows="3"
                        class="w-full rounded-md border bg-background px-3 py-2 font-normal"
                    />
                    <span
                        v-if="form.errors.remarks"
                        class="block text-xs text-destructive"
                        >{{ form.errors.remarks }}</span
                    >
                </label>
            </section>

            <section class="space-y-5 rounded-xl border bg-card p-5">
                <div class="space-y-2">
                    <h2 class="font-semibold">PO Item Allocations</h2>
                    <p class="text-sm text-muted-foreground">
                        Atur item PO dan jumlah yang dialokasikan ke shipment ini.
                    </p>
                </div>

                <Button
                    type="button"
                    variant="outline"
                    @click="addPurchaseOrderItem"
                >
                    <PlusCircle data-icon="inline-start" />
                    Add PO Item
                </Button>
                <span
                    v-if="form.errors.items"
                    class="block text-xs text-destructive"
                >
                    {{ form.errors.items }}
                </span>

                <div class="w-full overflow-x-auto rounded-md border">
                    <Table class="w-full min-w-[1150px]">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-12">No.</TableHead>
                                <TableHead class="min-w-[400px]">
                                    Purchase Order
                                </TableHead>
                                <TableHead class="min-w-[400px]">
                                    Purchase Order Item
                                </TableHead>
                                <TableHead class="min-w-[240px]">
                                    Quantity
                                </TableHead>
                                <TableHead class="min-w-[240px]">
                                    Unit
                                </TableHead>
                                <TableHead class="w-[86px] text-left">
                                    Action
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow
                                v-for="(row, itemIndex) in form.items"
                                :key="itemIndex"
                            >
                                <TableCell
                                    class="align-middle text-muted-foreground"
                                >
                                    {{ itemIndex + 1 }}
                                </TableCell>
                                <TableCell class="align-middle">
                                    <select
                                        v-model.number="row.purchase_order_id"
                                        class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        @change="
                                            row.purchase_order_item_id = null;
                                            row.product_id = null;
                                            row.product_code = '';
                                            row.product_name = '';
                                            row.quantity = null;
                                            row.unit = '';
                                        "
                                    >
                                        <option value="" disabled>
                                            {{
                                                availablePurchaseOrders.length
                                                    ? 'Select Purchase Order'
                                                    : 'No local PO items available'
                                            }}
                                        </option>
                                        <option
                                            v-for="purchaseOrder in availablePurchaseOrders"
                                            :key="purchaseOrder.id"
                                            :value="purchaseOrder.id"
                                        >
                                            {{
                                                purchaseOrder.supplier
                                                    ?.supplier_name
                                            }}
                                            - {{ purchaseOrder.po_number }}
                                        </option>
                                    </select>
                                    <span
                                        v-if="
                                            form.errors[
                                                `items.${itemIndex}.purchase_order_id`
                                            ]
                                        "
                                        class="text-xs text-destructive"
                                    >
                                        {{
                                            form.errors[
                                                `items.${itemIndex}.purchase_order_id`
                                            ]
                                        }}
                                    </span>
                                </TableCell>
                                <TableCell class="align-middle">
                                    <select
                                        v-model.number="
                                            row.purchase_order_item_id
                                        "
                                        :disabled="!row.purchase_order_id"
                                        class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        @change="selectPurchaseOrderItem(row)"
                                    >
                                        <option value="" disabled>
                                            Select PO item
                                        </option>
                                        <option
                                            v-for="item in getPurchaseOrderItems(row.purchase_order_id)"
                                            :key="item.id"
                                            :value="item.id"
                                        >
                                            {{ item.product_code }} -
                                            {{ item.product_name }}
                                        </option>
                                    </select>
                                    <span
                                        v-if="
                                            form.errors[
                                                `items.${itemIndex}.purchase_order_item_id`
                                            ]
                                        "
                                        class="text-xs text-destructive"
                                    >
                                        {{
                                            form.errors[
                                                `items.${itemIndex}.purchase_order_item_id`
                                            ]
                                        }}
                                    </span>
                                </TableCell>
                                <TableCell class="align-middle">
                                    <Input
                                        :model-value="row.quantity ?? ''"
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        :max="
                                            getSelectedRemainingQuantity(row)
                                        "
                                        :disabled="
                                            !row.purchase_order_item_id
                                        "
                                        :placeholder="
                                            String(
                                                getSelectedRemainingQuantity(
                                                    row,
                                                ) ?? '',
                                            )
                                        "
                                        class="min-w-[120px]"
                                        @update:model-value="
                                            updateQuantity(row, $event)
                                        "
                                    />
                                    <span
                                        v-if="
                                            form.errors[
                                                `items.${itemIndex}.quantity`
                                            ]
                                        "
                                        class="text-xs text-destructive"
                                    >
                                        {{
                                            form.errors[
                                                `items.${itemIndex}.quantity`
                                            ]
                                        }}
                                    </span>
                                </TableCell>
                                <TableCell class="align-middle">
                                    <Input
                                        :model-value="row.unit"
                                        type="text"
                                        disabled
                                        readonly
                                        placeholder="Unit"
                                    />
                                </TableCell>
                                <TableCell>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        :aria-label="`Remove PO item ${itemIndex + 1}`"
                                        @click="
                                            form.items.splice(itemIndex, 1)
                                        "
                                    >
                                        <Trash2
                                            data-icon="inline-start"
                                            class="text-destructive"
                                        />
                                    </Button>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="form.items.length === 0">
                                <TableCell
                                    :colspan="6"
                                    class="h-24 text-center text-muted-foreground"
                                >
                                    No PO items allocated yet. Add a row to
                                    select an item.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    class="flex flex-wrap justify-between gap-3 rounded-lg bg-muted/30 p-4 text-sm"
                >
                    <div>
                        <span class="text-muted-foreground">POs included:</span>
                        <strong>{{ selectedPos.size }}</strong>
                    </div>
                    <div>
                        <span class="text-muted-foreground">Item rows:</span>
                        <strong>{{ form.items.length }}</strong>
                    </div>
                </div>
                <p class="text-xs text-muted-foreground">
                    Product details come from the selected PO line. Allocated
                    quantities will update the PO line's remaining quantity
                    when saved.
                </p>
            </section>

            <footer class="flex justify-end gap-3">
                <Link
                    :href="index.url()"
                    class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-muted"
                    >Batal</Link
                >
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan…' : 'Simpan perubahan' }}
                </button>
            </footer>
        </form>
    </main>
</template>
