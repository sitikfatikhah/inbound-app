<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    CheckCircle2,
    ExternalLink,
    Filter,
    Package,
    Search,
    Warehouse as WarehouseIcon,
} from 'lucide-vue-next';
import PublicHeader from '@/components/public/PublicHeader.vue';

interface Receipt {
    id: number;
    arrival_number: string | null;
    received_date: string | null;
    status: 'Queued for Unloading' | 'Posted' | 'Received';
    shipment?: {
        id: number;
        shipment_number: string;
        eta: string | null;
        tracking_status: string | null;
    } | null;
    warehouse?: { id: number; warehouse_name: string } | null;
}
const props = defineProps<{ receipts?: Receipt[] }>();
const search = ref('');
const statusFilter = ref<'all' | Receipt['status']>('all');
const receipts = computed(() => props.receipts ?? []);
const filteredReceipts = computed(() =>
    receipts.value.filter((receipt) => {
        const keyword = search.value.trim().toLowerCase();
        const matchesSearch =
            !keyword ||
            [
                receipt.arrival_number,
                receipt.shipment?.shipment_number,
                receipt.warehouse?.warehouse_name,
                receipt.status,
            ].some((value) =>
                String(value ?? '')
                    .toLowerCase()
                    .includes(keyword),
            );
        return (
            matchesSearch &&
            (statusFilter.value === 'all' ||
                receipt.status === statusFilter.value)
        );
    }),
);
function formatDate(value: string | null) {
    return value
        ? new Intl.DateTimeFormat('id-ID', {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
          }).format(new Date(value))
        : '-';
}
function statusClass(status: Receipt['status']) {
    return status === 'Received'
        ? 'bg-emerald-50 text-emerald-700'
        : status === 'Posted'
          ? 'bg-blue-50 text-blue-700'
          : 'bg-amber-50 text-amber-700';
}
</script>

<template>
    <main class="min-h-screen bg-slate-50 text-slate-900">
        <div
            class="mx-auto w-full max-w-[1600px] px-4 pt-8 pb-8 sm:px-6 lg:px-8"
        >
            <section class="mb-6">
                <p
                    class="mb-1 text-[11px] font-bold tracking-[0.14em] text-primary uppercase"
                >
                    Warehouse Management
                </p>
                <div
                    class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
                >
                    <div>
                        <h1
                            class="text-3xl font-bold tracking-tight text-primary"
                        >
                            Arrival Warehouse
                        </h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Monitor kedatangan barang dan proses penerimaan
                            shipment.
                        </p>
                    </div>
                    <Link
                        href="/tracking/schedule"
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-3 py-2 text-xs font-semibold shadow-sm transition hover:bg-muted"
                        ><WarehouseIcon class="size-4" /> Jadwal
                        Kedatangan</Link
                    >
                </div>
            </section>
            <section
                class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
            >
                <div
                    v-for="stat in [
                        {
                            label: 'Total Penerimaan',
                            value: receipts.length,
                            icon: 'receipt',
                        },
                        {
                            label: 'Queued for Unloading',
                            value: receipts.filter(
                                (receipt) =>
                                    receipt.status === 'Queued for Unloading',
                            ).length,
                            icon: 'pending',
                        },
                        {
                            label: 'Posted',
                            value: receipts.filter(
                                (receipt) => receipt.status === 'Posted',
                            ).length,
                            icon: 'fact_check',
                        },
                        {
                            label: 'Received',
                            value: receipts.filter(
                                (receipt) => receipt.status === 'Received',
                            ).length,
                            icon: 'check',
                        },
                    ]"
                    :key="stat.label"
                    class="rounded-xl border border-border bg-card p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                {{ stat.label }}
                            </p>
                            <p
                                class="mt-3 text-2xl font-bold tracking-tight text-primary"
                            >
                                {{ stat.value }}
                            </p>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <span
                                class="material-symbols-outlined text-[20px]"
                                >{{ stat.icon }}</span
                            >
                        </div>
                    </div>
                </div>
            </section>
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                <section class="xl:col-span-8">
                    <div
                        class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
                    >
                        <div>
                            <h2 class="text-lg font-bold text-primary">
                                Barang Masuk
                            </h2>
                            <p class="mt-0.5 text-xs text-muted-foreground">
                                Daftar warehouse receipt dan status penerimaan.
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-3 py-2 text-xs font-semibold shadow-sm hover:bg-muted"
                                @click="
                                    statusFilter =
                                        statusFilter === 'all'
                                            ? 'Queued for Unloading'
                                            : 'all'
                                "
                            >
                                <Filter class="size-4" /> Filter
                            </button>
                            <div class="relative">
                                <Search
                                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                /><input
                                    v-model="search"
                                    class="h-9 w-48 rounded-lg border border-border bg-card pr-3 pl-9 text-xs outline-none focus:ring-2 focus:ring-primary/20"
                                    placeholder="Cari receipt..."
                                />
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="receipt in filteredReceipts"
                            :key="receipt.id"
                            class="group rounded-xl border border-l-4 border-border bg-card p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                            :class="
                                receipt.status === 'Received'
                                    ? 'border-l-emerald-500'
                                    : receipt.status === 'Posted'
                                      ? 'border-l-blue-500'
                                      : 'border-l-amber-500'
                            "
                        >
                            <div
                                class="flex flex-col gap-4 lg:flex-row lg:items-center"
                            >
                                <div
                                    class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                >
                                    <CheckCircle2
                                        v-if="receipt.status === 'Received'"
                                        class="size-6"
                                    /><Package v-else class="size-6" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="mb-1.5 flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="text-sm font-bold text-primary"
                                            >{{
                                                receipt.arrival_number ||
                                                `Receipt #${receipt.id}`
                                            }}</span
                                        ><span
                                            class="rounded-full px-2 py-0.5 text-[9px] font-bold tracking-wide uppercase"
                                            :class="statusClass(receipt.status)"
                                            >{{ receipt.status }}</span
                                        >
                                    </div>
                                    <p
                                        class="truncate text-sm font-semibold text-foreground"
                                    >
                                        {{
                                            receipt.shipment?.shipment_number ||
                                            'Shipment belum terhubung'
                                        }}
                                    </p>
                                    <div
                                        class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-muted-foreground"
                                    >
                                        <span>{{
                                            receipt.warehouse?.warehouse_name ||
                                            'Gudang belum diisi'
                                        }}</span
                                        ><span
                                            >ETA
                                            {{
                                                formatDate(
                                                    receipt.shipment?.eta ||
                                                        null,
                                                )
                                            }}</span
                                        ><span
                                            >Diterima
                                            {{
                                                formatDate(
                                                    receipt.received_date,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <Link
                                    v-if="receipt.shipment"
                                    :href="`/shipments/${receipt.shipment.id}`"
                                    class="shrink-0 rounded-md p-2 text-muted-foreground transition hover:bg-muted hover:text-primary"
                                    :aria-label="`Lihat ${receipt.shipment.shipment_number}`"
                                    ><ExternalLink class="size-5"
                                /></Link>
                            </div>
                        </div>
                        <div
                            v-if="!filteredReceipts.length"
                            class="rounded-xl border border-dashed bg-card p-16 text-center"
                        >
                            <WarehouseIcon
                                class="mx-auto size-10 text-muted-foreground"
                            />
                            <p class="mt-3 font-semibold">
                                Belum ada warehouse receipt
                            </p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Data penerimaan akan tampil setelah dicatat.
                            </p>
                        </div>
                    </div>
                </section>
                <aside class="xl:col-span-4">
                    <div
                        class="rounded-xl border border-border bg-card p-5 shadow-sm"
                    >
                        <h2 class="text-lg font-bold text-foreground">
                            Status penerimaan
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Distribusi status dari warehouse_receipts.
                        </p>
                        <div
                            class="mt-6 flex h-48 items-end justify-around gap-4 border-b border-border px-4"
                        >
                            <div
                                v-for="item in [
                                    {
                                        label: 'Queued',
                                        value: receipts.filter(
                                            (receipt) =>
                                                receipt.status ===
                                                'Queued for Unloading',
                                        ).length,
                                    },
                                    {
                                        label: 'Posted',
                                        value: receipts.filter(
                                            (receipt) =>
                                                receipt.status === 'Posted',
                                        ).length,
                                    },
                                    {
                                        label: 'Received',
                                        value: receipts.filter(
                                            (receipt) =>
                                                receipt.status === 'Received',
                                        ).length,
                                    },
                                ]"
                                :key="item.label"
                                class="flex h-full flex-1 flex-col items-center justify-end gap-2"
                            >
                                <div
                                    class="w-full rounded-t-lg bg-primary/70"
                                    :style="{
                                        height: `${Math.max((item.value / Math.max(receipts.length, 1)) * 100, item.value ? 12 : 2)}%`,
                                    }"
                                />
                                <span
                                    class="text-[10px] text-muted-foreground"
                                    >{{ item.label }}</span
                                ><strong class="text-sm">{{
                                    item.value
                                }}</strong>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>
