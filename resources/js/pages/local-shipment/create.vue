<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, PlusCircle, Trash2 } from 'lucide-vue-next';
import {
    index,
    store,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import PurchaseOrderController from '@/actions/App/Http/Controllers/PurchaseOrderController';

interface PurchaseOrder {
    id: number;
    po_number: string;
    supplier?: { supplier_name: string } | null;
}

interface Warehouse {
    id: number;
    warehouse_name: string;
}

interface Milestone {
    milestone_name: string;
    status: string;
    planned_date: string;
    actual_date: string;
    remarks: string;
}

const props = defineProps<{
    shipment_number: string;
    purchaseOrders: PurchaseOrder[];
    warehouses: Warehouse[];
}>();

interface ShipmentItemRow {
    purchase_order_id: number | null;
    purchase_order_item_id: number | null;
    product_code: string;
    quantity: number | null;
}

const form = useForm({
    shipment_number: props.shipment_number,
    purchase_order_ids: [] as number[],
    warehouse_id: '',
    delivery_order_number: '',
    etd: '',
    atd: '',
    status_document: 'purchase order created',
    tracking_status: 'not shipped',
    remarks: '',
    item: [] as ShipmentItemRow[],
    milestones: [] as Milestone[],
});

const selectedPos = computed(() =>
    new Set(form.item.map((item) => item.purchase_order_id).filter(Boolean)),
);

const addPurchaseOrderItem = () => {
    form.item.push({
        purchase_order_id: null,
        purchase_order_item_id: null,
        product_code: '',
        quantity: null,
    });
};

function addMilestone() {
    form.milestones.push({
        milestone_name: '',
        status: 'Planned',
        planned_date: '',
        actual_date: '',
        remarks: '',
    });
}
function submit() {
    form.post(store.url(), { preserveScroll: true });
}
</script>

<template>
    <Head title="Buat Local Shipment" />

    <main class="mx-auto w-full space-y-6 sm:p-8 sm:mx-0 mx-4 rounded-lg border bg-card ml-8">
        <header class="space-y-2">
            <Link
                :href="index.url()"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4"></ArrowLeft>
                Back to Local Shipments         
            </Link>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">Add Local Shipment</h1>
            <p class="text-sm text-muted-foreground">
                Create a Local Shipment and allocate quantities.
            </p>
        </header>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="space-y-5 rounded-xl border bg-card">
                <h2 class="font-semibold p-4">Informasi Pengiriman</h2>

                <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="space-y-2 text-sm font-medium">
                        Shipment Number
                        <input
                            v-model="form.shipment_number"
                            readonly
                            class="h-10 w-full rounded-md border bg-muted px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.shipment_number"
                            class="block text-xs text-destructive"
                        >
                            {{ form.errors.shipment_number }}
                        </span>
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
                        >
                            {{ form.errors.warehouse_id }}
                        </span>
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
                        >
                            {{ form.errors.delivery_order_number }}
                        </span>
                    </label>
                    <label class="space-y-2 text-sm font-medium">
                        Status dokumen
                        <input
                            v-model="form.status_document"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.status_document"
                            class="block text-xs text-destructive"
                        >
                            {{ form.errors.status_document }}
                        </span>
                    </label>

                    <label class="space-y-2 text-sm font-medium">
                        Status tracking
                        <input
                            v-model="form.tracking_status"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.tracking_status"
                            class="block text-xs text-destructive"
                        >
                            {{ form.errors.tracking_status }}
                        </span>
                    </label>
                </div>
            </section>
            <section class="rounded-lg border bg-card p-5">
                <Tabs default-value="containers" class="w-full">
                <TabsList class="flex h-auto flex-wrap">
                    <TabsTrigger value="containers">PurchaseOrderItems</TabsTrigger>
                    <TabsTrigger value="schedule">Shipment Schedule</TabsTrigger>
                    <TabsTrigger value="related">Related Information</TabsTrigger>
                </TabsList>

                <TabsContent value="containers" class="space-y-5 pt-5">                 
                    <div class="space-y-4 border-t pt-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h4 class="font-medium">PO Line PurchaseOrderItems</h4>
                            <p class="text-sm text-muted-foreground">Each item is one table row. Add items to append rows to this table.</p>
                        </div>
                        <Button type="button" variant="outline" @click="addPurchaseOrderItem">
                            <PlusCircle class="mr-2 h-4 w-4" /> Add PO Item
                        </Button>
                        </div>

                        <div class="w-full overflow-x-auto rounded-md border">
                        <Table class="w-full min-w-[1150px]">
                            <TableHeader>
                            <TableRow>
                                <TableHead class="w-12">No.</TableHead>
                                <TableHead class="min-w-[300px]">Purchase Order</TableHead>
                                <TableHead class="min-w-[300px]">Purchase Order PurchaseOrderItem</TableHead>
                                <TableHead class="w-[180px]">Quantity</TableHead>
                                <TableHead class="min-w-[360px]">Unit</TableHead>
                                <TableHead class="w-[72px] text-right">Action</TableHead>
                            </TableRow>
                            </TableHeader>

                            <TableBody>
                            <TableRow v-for="(row, itemIndex) in form.item" :key="itemIndex">
                                <TableCell class="align-middle text-muted-foreground">
                                {{ itemIndex + 1 }}
                                </TableCell>

                                <TableCell class="align-middle">
                                <select
                                    :value="row.purchase_order_id ?? ''"
                                    class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="" disabled>Select PO item</option>
                            
                                </select>
                                </TableCell>
                                <TableCell class="align-middle">
                                <select
                                    :value="row.product_code ?? ''"
                                    class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="" disabled>Select PO item</option>
                            
                                </select>
                        
                                </TableCell>

                                <TableCell class="align-middle">
                                <Input
                                    :model-Value="row.quantity"
                                    @update:model-value="row.quantity = Number($event) || 0"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    :disabled="!row.product_code"
                                    placeholder="0"
                                    class="min-w-[120px]"
                                />
                                </TableCell>
                                <TableCell class="text-right align-middle">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    aria-label="Remove PO item"
                                    @click="form.item.splice(itemIndex, 1)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="form.item.length === 0">
                                <TableCell :colspan="6" class="h-24 text-center text-muted-foreground">
                                No PO items assigned to this container. Click “Add PO PurchaseOrderItem” to add a row.
                                </TableCell>
                            </TableRow>
                            </TableBody>
                        </Table>
                        </div>
                        <p class="text-xs text-muted-foreground">
                        {{ form.item.length }} item row(s)
                        </p>
                    </div>
                    <div class="flex flex-wrap justify-between gap-3 rounded-lg bg-muted/30 p-4">
                    <div class="text-sm"><span class="text-muted-foreground">POs included:</span> <strong>{{ selectedPos.size }}</strong></div>
                    </div>
                    <p class="text-xs text-muted-foreground">The PO line's quantity, product details, and total_price are not edited here. On save, the server must deduct the summed container quantity from that line's backorder_qty inside a database transaction.</p>
                </TabsContent>

                <TabsContent value="schedule" class="pt-5">
                    <h2 class="mb-4 font-semibold">Shipment Schedule</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-2"><label class="text-sm font-medium">ETD</label><Input v-model="form.etd" type="date" /></div>
                    <div class="space-y-2"><label class="text-sm font-medium">Actual Departure (ATD)</label><Input v-model="form.atd" type="date" /></div>
                    </div>
                </TabsContent>
                <TabsContent value="related" class="pt-5">
                    <h2 class="mb-4 font-semibold">Related Information</h2>
                    
                <label class="block grid gap-5 p-3">
                    Catatan
                    <textarea
                        v-model="form.remarks"
                        rows="3"
                        class="rounded-md border bg-background px-3 py-2 font-normal"
                    ></textarea>
                    <span
                        v-if="form.errors.remarks"
                        class="block text-xs text-destructive"
                        >{{ form.errors.remarks }}
                    </span>
                </label>
                </TabsContent>
                </Tabs>
            </section>
            <section class="space-y-5 rounded-xl border bg-card p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">Milestone</h2>
                        <p class="text-sm text-muted-foreground">
                            Status dan tanggal mengikuti format milestone
                            shipment.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm font-medium hover:bg-muted"
                        @click="addMilestone"
                    >
                        <Plus class="size-4" /> Tambah milestone
                    </button>
                </div>

                <div
                    v-for="(milestone, milestoneIndex) in form.milestones"
                    :key="milestoneIndex"
                    class="space-y-4 rounded-lg border p-4"
                >
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold">
                            Milestone {{ milestoneIndex + 1 }}
                        </h3>
                        <button
                            type="button"
                            class="rounded-md p-2 text-destructive hover:bg-destructive/10"
                            :aria-label="`Hapus milestone ${milestoneIndex + 1}`"
                            @click="form.milestones.splice(milestoneIndex, 1)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="space-y-2 text-sm font-medium">
                            Nama milestone
                            <input
                                v-model="milestone.milestone_name"
                                required
                                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                            />
                            <span
                                v-if="
                                    form.errors[
                                        `milestones.${milestoneIndex}.milestone_name`
                                    ]
                                "
                                class="block text-xs text-destructive"
                                >{{
                                    form.errors[
                                        `milestones.${milestoneIndex}.milestone_name`
                                    ]
                                }}</span
                            >
                        </label>
                        <label class="space-y-2 text-sm font-medium">
                            Status
                            <select
                                v-model="milestone.status"
                                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                            >
                                <option>Planned</option>
                                <option>In Progress</option>
                                <option>Completed</option>
                            </select>
                            <span
                                v-if="
                                    form.errors[
                                        `milestones.${milestoneIndex}.status`
                                    ]
                                "
                                class="block text-xs text-destructive"
                                >{{
                                    form.errors[
                                        `milestones.${milestoneIndex}.status`
                                    ]
                                }}</span
                            >
                        </label>
                        <label class="space-y-2 text-sm font-medium">
                            Planned date
                            <input
                                v-model="milestone.planned_date"
                                type="date"
                                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                            />
                            <span
                                v-if="
                                    form.errors[
                                        `milestones.${milestoneIndex}.planned_date`
                                    ]
                                "
                                class="block text-xs text-destructive"
                                >{{
                                    form.errors[
                                        `milestones.${milestoneIndex}.planned_date`
                                    ]
                                }}</span
                            >
                        </label>
                        <label class="space-y-2 text-sm font-medium">
                            Actual date
                            <input
                                v-model="milestone.actual_date"
                                type="date"
                                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                            />
                            <span
                                v-if="
                                    form.errors[
                                        `milestones.${milestoneIndex}.actual_date`
                                    ]
                                "
                                class="block text-xs text-destructive"
                                >{{
                                    form.errors[
                                        `milestones.${milestoneIndex}.actual_date`
                                    ]
                                }}</span
                            >
                        </label>
                    </div>
                    <label class="block space-y-2 text-sm font-medium">
                        Catatan milestone
                        <textarea
                            v-model="milestone.remarks"
                            rows="2"
                            class="w-full rounded-md border bg-background px-3 py-2 font-normal"
                        />
                        <span
                            v-if="
                                form.errors[
                                    `milestones.${milestoneIndex}.remarks`
                                ]
                            "
                            class="block text-xs text-destructive"
                            >{{
                                form.errors[
                                    `milestones.${milestoneIndex}.remarks`
                                ]
                            }}</span
                        >
                    </label>
                </div>

                <p
                    v-if="!form.milestones.length"
                    class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground"
                >
                    Belum ada milestone. Tambahkan milestone jika diperlukan.
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
                    {{ form.processing ? 'Menyimpan…' : 'Simpan shipment' }}
                </button>
            </footer>
        </form>
    </main>
</template>
