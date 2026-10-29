<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    create,
    destroy,
    edit,
    show,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';

interface LocalShipment {
    id: number;
    shipment_number: string;
    delivery_order_number: string | null;
    etd: string | null;
    atd: string | null;
    status_document: string | null;
    tracking_status: string | null;
    purchase_order?: {
        po_number: string;
        supplier?: { supplier_name: string } | null;
    } | null;
    warehouse?: { warehouse_name: string } | null;
    milestones: Array<{ status: string }>;
}

const props = defineProps<{ localShipments: LocalShipment[] }>();
const search = ref('');

const filteredShipments = computed(() => {
    const keyword = search.value.trim().toLocaleLowerCase();

    if (!keyword) {
        return props.localShipments;
    }

    return props.localShipments.filter((shipment) =>
        [
            shipment.shipment_number,
            shipment.purchase_order?.po_number,
            shipment.purchase_order?.supplier?.supplier_name,
            shipment.warehouse?.warehouse_name,
            shipment.delivery_order_number,
            shipment.tracking_status,
            shipment.status_document,
        ].some((value) => value?.toLocaleLowerCase().includes(keyword)),
    );
});

function dateLabel(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
    }).format(new Date(value));
}

function deleteShipment(shipment: LocalShipment) {
    if (
        window.confirm(
            `Hapus ${shipment.shipment_number} beserta milestone-nya?`,
        )
    ) {
        router.delete(destroy.url(shipment.id));
    }
}
</script>

<template>
    <Head title="Local Shipment" />

    <main class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold">Local Shipment</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola shipment lokal, status, dan milestone.
                </p>
            </div>
            <Link
                :href="create.url()"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90"
            >
                Buat shipment
            </Link>
        </header>

        <label class="block max-w-md space-y-2 text-sm font-medium">
            Cari shipment
            <input
                v-model="search"
                type="search"
                placeholder="Nomor, PO, vendor, gudang..."
                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
            />
        </label>

        <div class="overflow-x-auto rounded-xl border bg-card">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="border-b bg-muted/40 text-muted-foreground">
                    <tr>
                        <th class="p-4 font-medium">Shipment</th>
                        <th class="p-4 font-medium">PO / Vendor</th>
                        <th class="p-4 font-medium">Gudang</th>
                        <th class="p-4 font-medium">ETD / ATD</th>
                        <th class="p-4 font-medium">Tracking</th>
                        <th class="p-4 font-medium">Milestone</th>
                        <th class="p-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="shipment in filteredShipments"
                        :key="shipment.id"
                        class="border-b last:border-0"
                    >
                        <td class="p-4">
                            <Link
                                :href="show.url(shipment.id)"
                                class="font-semibold text-primary hover:underline"
                                >{{ shipment.shipment_number }}</Link
                            >
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    shipment.delivery_order_number ||
                                    'Tanpa nomor delivery order'
                                }}
                            </p>
                        </td>
                        <td class="p-4">
                            <p>
                                {{ shipment.purchase_order?.po_number || '-' }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    shipment.purchase_order?.supplier
                                        ?.supplier_name || '-'
                                }}
                            </p>
                        </td>
                        <td class="p-4">
                            {{ shipment.warehouse?.warehouse_name || '-' }}
                        </td>
                        <td class="p-4">
                            <p>ETD {{ dateLabel(shipment.etd) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                ATD {{ dateLabel(shipment.atd) }}
                            </p>
                        </td>
                        <td class="p-4">
                            <p class="font-medium">
                                {{ shipment.tracking_status || '-' }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ shipment.status_document || '-' }}
                            </p>
                        </td>
                        <td class="p-4">{{ shipment.milestones.length }}</td>
                        <td class="p-4">
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="edit.url(shipment.id)"
                                    class="rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-muted"
                                    >Edit</Link
                                >
                                <button
                                    type="button"
                                    class="rounded-md border border-destructive/30 px-3 py-1.5 text-xs font-medium text-destructive hover:bg-destructive/10"
                                    @click="deleteShipment(shipment)"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!filteredShipments.length">
                        <td
                            colspan="7"
                            class="p-10 text-center text-muted-foreground"
                        >
                            {{
                                search
                                    ? 'Tidak ada shipment yang cocok dengan pencarian.'
                                    : 'Belum ada local shipment.'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-sm text-muted-foreground">
            Menampilkan {{ filteredShipments.length }} dari
            {{ localShipments.length }} shipment.
        </p>
    </main>
</template>
