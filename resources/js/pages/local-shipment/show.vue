<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    edit,
    index,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';

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
        supplier?: { supplier_name: string } | null;
    } | null;
    warehouse?: { warehouse_name: string } | null;
    milestones: Milestone[];
}

const { localShipment } = defineProps<{ localShipment: LocalShipment }>();

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

function milestoneClass(status: string): string {
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
    <Head :title="localShipment.shipment_number" />

    <main class="mx-auto w-full max-w-5xl space-y-6 p-4 sm:p-8">
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
                    Detail pengiriman lokal dan progres milestone.
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
                        {{ localShipment.purchase_order?.po_number || '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground">Vendor</dt>
                    <dd class="mt-1 font-medium">
                        {{
                            localShipment.purchase_order?.supplier
                                ?.supplier_name || '-'
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

        <section class="overflow-hidden rounded-xl border bg-card">
            <div class="border-b p-5">
                <h2 class="font-semibold">Milestone</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Planned date, actual date, status, dan catatan kegiatan.
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="border-b bg-muted/40 text-muted-foreground">
                        <tr>
                            <th class="p-4 font-medium">Milestone</th>
                            <th class="p-4 font-medium">Planned date</th>
                            <th class="p-4 font-medium">Actual date</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="milestone in localShipment.milestones"
                            :key="milestone.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-4 font-medium">
                                {{ milestone.milestone_name }}
                            </td>
                            <td class="p-4">
                                {{ dateLabel(milestone.planned_date) }}
                            </td>
                            <td class="p-4">
                                {{ dateLabel(milestone.actual_date) }}
                            </td>
                            <td class="p-4">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="milestoneClass(milestone.status)"
                                    >{{ milestone.status }}</span
                                >
                            </td>
                            <td class="p-4">{{ milestone.remarks || '-' }}</td>
                        </tr>
                        <tr v-if="!localShipment.milestones.length">
                            <td
                                colspan="5"
                                class="p-8 text-center text-muted-foreground"
                            >
                                Belum ada milestone.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
