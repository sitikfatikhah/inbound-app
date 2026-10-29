<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import {
    index,
    update,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';

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
    status: 'Planned' | 'In Progress' | 'Completed';
    planned_date: string | null;
    actual_date: string | null;
    remarks: string | null;
}

interface LocalShipment {
    id: number;
    shipment_number: string;
    purchase_orders_id: number;
    warehouse_id: number;
    delivery_order_number: string | null;
    etd: string | null;
    atd: string | null;
    status_document: string | null;
    tracking_status: string | null;
    remarks: string | null;
}

const props = defineProps<{
    localShipment: LocalShipment;
    milestones: Milestone[];
    purchaseOrders: PurchaseOrder[];
    warehouses: Warehouse[];
}>();

const form = useForm({
    shipment_number: props.localShipment.shipment_number,
    purchase_orders_id: String(props.localShipment.purchase_orders_id),
    warehouse_id: String(props.localShipment.warehouse_id),
    delivery_order_number: props.localShipment.delivery_order_number ?? '',
    etd: props.localShipment.etd ?? '',
    atd: props.localShipment.atd ?? '',
    status_document: props.localShipment.status_document ?? '',
    tracking_status: props.localShipment.tracking_status ?? '',
    remarks: props.localShipment.remarks ?? '',
    milestones: props.milestones.map((milestone) => ({
        milestone_name: milestone.milestone_name,
        status: milestone.status,
        planned_date: milestone.planned_date ?? '',
        actual_date: milestone.actual_date ?? '',
        remarks: milestone.remarks ?? '',
    })),
});

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
    form.put(update.url(props.localShipment.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit ${localShipment.shipment_number}`" />

    <main class="mx-auto w-full max-w-5xl space-y-6 p-4 sm:p-8">
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
                        Purchase order lokal
                        <select
                            v-model="form.purchase_orders_id"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        >
                            <option value="" disabled>
                                Pilih purchase order
                            </option>
                            <option
                                v-for="order in purchaseOrders"
                                :key="order.id"
                                :value="String(order.id)"
                            >
                                {{ order.po_number
                                }}{{
                                    order.supplier?.supplier_name
                                        ? ` — ${order.supplier.supplier_name}`
                                        : ''
                                }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.purchase_orders_id"
                            class="block text-xs text-destructive"
                            >{{ form.errors.purchase_orders_id }}</span
                        >
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
                        <input
                            v-model="form.status_document"
                            required
                            class="h-10 w-full rounded-md border bg-background px-3 font-normal"
                        />
                        <span
                            v-if="form.errors.status_document"
                            class="block text-xs text-destructive"
                            >{{ form.errors.status_document }}</span
                        >
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
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">Milestone</h2>
                        <p class="text-sm text-muted-foreground">
                            Perubahan milestone disimpan bersama shipment.
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
                    {{ form.processing ? 'Menyimpan…' : 'Simpan perubahan' }}
                </button>
            </footer>
        </form>
    </main>
</template>
