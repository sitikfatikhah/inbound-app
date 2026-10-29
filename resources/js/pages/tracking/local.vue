<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ClipboardList, Search, Truck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Milestone {
    id: number;
    milestone_name: string;
    status: 'Planned' | 'In Progress' | 'Completed';
    planned_date: string | null;
    actual_date: string | null;
    remarks: string | null;
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
    purchase_order?: {
        po_number: string;
        supplier?: { supplier_name: string; country: string | null } | null;
    } | null;
    warehouse?: { warehouse_name: string } | null;
    milestones: Milestone[];
}

const props = defineProps<{ localShipments: LocalShipment[] }>();
const search = ref('');
const statusFilter = ref<'all' | 'active' | 'completed'>('all');

const completedShipments = computed(
    () => props.localShipments.filter(isCompleted).length,
);
const filteredShipments = computed(() => {
    const keyword = search.value.trim().toLocaleLowerCase();

    return props.localShipments.filter((shipment) => {
        const matchesSearch =
            !keyword ||
            [
                shipment.shipment_number,
                shipment.purchase_order?.po_number,
                shipment.purchase_order?.supplier?.supplier_name,
                shipment.purchase_order?.supplier?.country,
                shipment.warehouse?.warehouse_name,
                shipment.delivery_order_number,
                shipment.status_document,
                shipment.tracking_status,
                ...shipment.milestones.map(
                    (milestone) => milestone.milestone_name,
                ),
            ].some((value) => value?.toLocaleLowerCase().includes(keyword));

        const matchesStatus =
            statusFilter.value === 'all' ||
            (statusFilter.value === 'completed' && isCompleted(shipment)) ||
            (statusFilter.value === 'active' && !isCompleted(shipment));

        return matchesSearch && matchesStatus;
    });
});

function isCompleted(shipment: LocalShipment): boolean {
    const status = shipment.tracking_status?.toLocaleLowerCase() ?? '';

    return (
        /complete|delivered|arrived|received/.test(status) ||
        (shipment.milestones.length > 0 &&
            shipment.milestones.every(
                (milestone) => milestone.status === 'Completed',
            ))
    );
}

function formatDate(value: string | null): string {
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

function statusClass(status: string): string {
    if (status === 'Completed') {
        return 'bg-emerald-500/10 text-emerald-700';
    }

    if (status === 'In Progress') {
        return 'bg-amber-500/10 text-amber-700';
    }

    return 'bg-muted text-muted-foreground';
}
</script>

<template>
    <Head title="Tracking Local" />

    <main
        class="min-h-screen w-full bg-slate-50 px-4 py-8 text-slate-900 lg:px-8"
    >
        <div class="mx-auto max-w-7xl space-y-8">
            <header
                class="flex flex-col justify-between gap-4 md:flex-row md:items-end"
            >
                <div>
                    <nav
                        class="mb-2 flex items-center gap-2 text-xs text-muted-foreground"
                    >
                        <span>Dashboard</span><span>/</span>
                        <span class="font-semibold text-primary"
                            >Tracking Local</span
                        >
                    </nav>
                    <h1 class="text-3xl font-bold tracking-tight text-primary">
                        Tracking Local
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Pantau status pengiriman lokal dan progres milestone
                        setiap shipment.
                    </p>
                </div>
                <div class="relative w-full md:max-w-sm">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari shipment, PO, vendor, milestone..."
                        class="h-10 w-full rounded-lg border border-border bg-card pr-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                    />
                </div>
            </header>

            <section class="grid gap-4 sm:grid-cols-3">
                <article
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="mb-3 flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <ClipboardList class="size-5" />
                    </div>
                    <p
                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        Total shipment lokal
                    </p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ localShipments.length }}
                    </p>
                </article>
                <article
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="mb-3 flex size-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600"
                    >
                        <Truck class="size-5" />
                    </div>
                    <p
                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        Dalam proses
                    </p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ localShipments.length - completedShipments }}
                    </p>
                </article>
                <article
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <p
                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        Selesai
                    </p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ completedShipments }}
                    </p>
                    <p class="mt-2 text-xs text-muted-foreground">
                        Berdasarkan status tracking atau milestone.
                    </p>
                </article>
            </section>

            <section class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-primary">
                            Daftar local shipment
                        </h2>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Milestone menampilkan status dan tanggal
                            planned/actual dari data shipment.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="filter in [
                                { value: 'all', label: 'Semua' },
                                { value: 'active', label: 'Berjalan' },
                                { value: 'completed', label: 'Selesai' },
                            ] as const"
                            :key="filter.value"
                            type="button"
                            class="rounded-full border px-3 py-1.5 text-xs font-semibold"
                            :class="
                                statusFilter === filter.value
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-border bg-card text-muted-foreground'
                            "
                            @click="statusFilter = filter.value"
                        >
                            {{ filter.label }}
                        </button>
                    </div>
                </div>

                <div
                    v-for="shipment in filteredShipments"
                    :key="shipment.id"
                    class="overflow-hidden rounded-xl border border-border bg-card shadow-sm"
                >
                    <div
                        class="flex flex-col justify-between gap-4 border-b p-5 md:flex-row md:items-start"
                    >
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-primary">
                                    {{ shipment.shipment_number }}
                                </h3>
                                <span
                                    class="rounded-full bg-primary/10 px-2.5 py-1 text-[10px] font-bold text-primary uppercase"
                                >
                                    {{
                                        shipment.tracking_status ||
                                        'Belum ada status'
                                    }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{
                                    shipment.purchase_order?.po_number ||
                                    'PO tidak tersedia'
                                }}
                                ·
                                {{
                                    shipment.purchase_order?.supplier
                                        ?.supplier_name ||
                                    'Vendor tidak tersedia'
                                }}
                                ·
                                {{
                                    shipment.warehouse?.warehouse_name ||
                                    'Gudang tidak tersedia'
                                }}
                            </p>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Delivery order
                                </dt>
                                <dd class="font-medium">
                                    {{ shipment.delivery_order_number || '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Dokumen
                                </dt>
                                <dd class="font-medium">
                                    {{ shipment.status_document || '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    ETD
                                </dt>
                                <dd class="font-medium">
                                    {{ formatDate(shipment.etd) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    ATD
                                </dt>
                                <dd class="font-medium">
                                    {{ formatDate(shipment.atd) }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="p-5">
                        <h4 class="mb-3 text-sm font-semibold">
                            Milestone ({{ shipment.milestones.length }})
                        </h4>
                        <ol
                            v-if="shipment.milestones.length"
                            class="grid gap-3 md:grid-cols-2"
                        >
                            <li
                                v-for="milestone in shipment.milestones"
                                :key="milestone.id"
                                class="rounded-lg border p-4"
                            >
                                <div
                                    class="flex flex-wrap items-start justify-between gap-2"
                                >
                                    <p class="font-medium">
                                        {{ milestone.milestone_name }}
                                    </p>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                        :class="statusClass(milestone.status)"
                                    >
                                        {{ milestone.status }}
                                    </span>
                                </div>
                                <dl class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <dt class="text-muted-foreground">
                                            Planned
                                        </dt>
                                        <dd class="mt-1 font-medium">
                                            {{
                                                formatDate(
                                                    milestone.planned_date,
                                                )
                                            }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-muted-foreground">
                                            Actual
                                        </dt>
                                        <dd class="mt-1 font-medium">
                                            {{
                                                formatDate(
                                                    milestone.actual_date,
                                                )
                                            }}
                                        </dd>
                                    </div>
                                </dl>
                                <p
                                    v-if="milestone.remarks"
                                    class="mt-3 text-xs text-muted-foreground"
                                >
                                    {{ milestone.remarks }}
                                </p>
                            </li>
                        </ol>
                        <p
                            v-else
                            class="rounded-lg border border-dashed p-5 text-center text-sm text-muted-foreground"
                        >
                            Belum ada milestone untuk shipment ini.
                        </p>
                        <p
                            v-if="shipment.remarks"
                            class="mt-4 text-sm whitespace-pre-line text-muted-foreground"
                        >
                            {{ shipment.remarks }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="!filteredShipments.length"
                    class="rounded-xl border border-dashed bg-card p-12 text-center"
                >
                    <ClipboardList
                        class="mx-auto size-10 text-muted-foreground"
                    />
                    <p class="mt-3 font-semibold">
                        {{
                            localShipments.length
                                ? 'Tidak ada shipment yang cocok'
                                : 'Belum ada local shipment'
                        }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{
                            localShipments.length
                                ? 'Ubah kata kunci atau filter status.'
                                : 'Shipment lokal akan muncul di sini setelah dibuat.'
                        }}
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>
