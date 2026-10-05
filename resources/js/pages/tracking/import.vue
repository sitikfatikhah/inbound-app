<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowRight,
    Check,
    ExternalLink,
    Filter,
    PackageSearch,
    Ship,
    Truck,
} from 'lucide-vue-next';
import PublicHeader from '@/components/public/PublicHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Milestone {
    id: number;
    milestone_name: string;
    status: 'Planned' | 'In Progress' | 'Completed';
    planned_date: string | null;
    actual_date: string | null;
    remarks: string | null;
}
interface Shipment {
    id: number;
    shipment_number: string;
    bl_number: string | null;
    vessel_name: string | null;
    voyage_number: string | null;
    etd: string | null;
    eta: string | null;
    ata: string | null;
    status_document: string | null;
    tracking_status: string | null;
    remarks: string | null;
    forwarder?: { forwarder_name: string } | null;
    shipping_line?: { shipping_line_name: string } | null;
    port_of_loading?: { port_name: string } | null;
    port_of_discharge?: { port_name: string } | null;
    warehouse?: { warehouse_name: string } | null;
    milestones?: Milestone[];
}
const props = defineProps<{ shipments?: Shipment[] }>();
const search = ref('');
const filter = ref<'all' | 'active' | 'exception' | 'completed'>('all');
const shipments = computed(() => props.shipments ?? []);
const filteredShipments = computed(() =>
    shipments.value.filter((shipment) => {
        const keyword = search.value.trim().toLowerCase();
        const matchesSearch =
            !keyword ||
            [
                shipment.shipment_number,
                shipment.bl_number,
                shipment.vessel_name,
                shipment.tracking_status,
                shipment.port_of_discharge?.port_name,
            ].some((value) =>
                String(value ?? '')
                    .toLowerCase()
                    .includes(keyword),
            );
        const status = (shipment.tracking_status ?? '').toLowerCase();
        const matchesFilter =
            filter.value === 'all' ||
            (filter.value === 'active' &&
                !/delay|exception|complete|delivered/.test(status)) ||
            (filter.value === 'exception' && /delay|exception/.test(status)) ||
            (filter.value === 'completed' &&
                /complete|delivered|arrived/.test(status));
        return matchesSearch && matchesFilter;
    }),
);
const completedCount = computed(
    () =>
        shipments.value.filter(
            (shipment) =>
                /complete|delivered|arrived/i.test(
                    shipment.tracking_status ?? '',
                ) || shipment.ata,
        ).length,
);
const exceptionCount = computed(
    () =>
        shipments.value.filter((shipment) =>
            /delay|exception/i.test(shipment.tracking_status ?? ''),
        ).length,
);
const activeCount = computed(
    () =>
        shipments.value.filter(
            (shipment) =>
                !/complete|delivered|arrived/i.test(
                    shipment.tracking_status ?? '',
                ) && !shipment.ata,
        ).length,
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
function progress(shipment: Shipment) {
    const milestones = shipment.milestones ?? [];
    return milestones.length
        ? Math.round(
              (milestones.filter(
                  (milestone) => milestone.status === 'Completed',
              ).length /
                  milestones.length) *
                  100,
          )
        : shipment.ata
          ? 100
          : shipment.tracking_status
            ? 40
            : 0;
}
function isException(shipment: Shipment) {
    return /delay|exception/i.test(shipment.tracking_status ?? '');
}
function milestoneClass(status: Milestone['status']) {
    return status === 'Completed'
        ? 'bg-emerald-500 text-white'
        : status === 'In Progress'
          ? 'bg-primary text-white'
          : 'border border-border bg-muted text-muted-foreground';
}
</script>

<template>
    <main class="min-h-screen w-full bg-slate-50 pt-8 pb-12 text-slate-900 ml-0 lg:px-8">
        <header
            class="mb-8 flex flex-col justify-between gap-4 lg:flex-row"
        >
            <div mx-auto w-full max-w-[1600px] px-4 pt-24 pb-8 sm:px-6 lg:px-8>
                <p
                    class="mb-1 text-xs font-semibold tracking-[0.18em] text-primary uppercase"
                >
                    Supply Chain Control Tower
                </p>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Monitoring Pengapalan
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Pantau status shipment import, dokumen, dan milestone secara
                    terpusat.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <Link
                    href="/tracking/schedule"
                    class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium transition hover:bg-muted"
                    ><Ship class="size-4" /> Jadwal Kedatangan</Link
                ><Button
                    variant="outline"
                    @click="filter = filter === 'all' ? 'exception' : 'all'"
                    ><Filter data-icon="inline-start" />
                    {{
                        filter === 'exception' ? 'Semua Shipment' : 'Exception'
                    }}</Button
                >
            </div>
        </header>
        <section
            class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4"
        >
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <div class="mb-4 flex items-start justify-between">
                    <div class="rounded-lg bg-primary/10 p-2.5 text-primary">
                        <Ship class="size-5" />
                    </div>
                    <span class="text-xs font-bold text-primary">Aktual</span>
                </div>
                <p
                    class="mb-1 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Shipment Aktif
                </p>
                <p class="text-2xl font-bold text-foreground">
                    {{ activeCount }}
                    <span class="text-sm font-normal text-muted-foreground"
                        >Shipment</span
                    >
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <div
                    class="mb-4 w-fit rounded-lg bg-amber-500/10 p-2.5 text-amber-600"
                >
                    <PackageSearch class="size-5" />
                </div>
                <p
                    class="mb-1 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Status Dokumen
                </p>
                <p class="text-2xl font-bold text-foreground">
                    {{
                        shipments.filter((shipment) => shipment.status_document)
                            .length
                    }}
                    <span class="text-sm font-normal text-muted-foreground"
                        >Terdata</span
                    >
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <div
                    class="mb-4 w-fit rounded-lg bg-destructive/10 p-2.5 text-destructive"
                >
                    <AlertTriangle class="size-5" />
                </div>
                <p
                    class="mb-1 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Pengecualian
                </p>
                <p class="text-2xl font-bold text-foreground">
                    {{ exceptionCount }}
                    <span class="text-sm font-normal text-muted-foreground"
                        >Isu</span
                    >
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <div
                    class="mb-4 w-fit rounded-lg bg-emerald-500/10 p-2.5 text-emerald-600"
                >
                    <Check class="size-5" />
                </div>
                <p
                    class="mb-1 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Selesai
                </p>
                <p class="text-2xl font-bold text-foreground">
                    {{ completedCount }}
                    <span class="text-sm font-normal text-muted-foreground"
                        >Shipment</span
                    >
                </p>
            </div>
        </section>
        <section class="space-y-6">
            <article
                v-for="shipment in filteredShipments"
                :key="shipment.id"
                class="group overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all hover:shadow-md"
                :class="{
                    'border-l-4 border-l-destructive': isException(shipment),
                    'opacity-80': shipment.ata,
                }"
            >
                <div class="border-b border-border bg-muted/20 p-6">
                    <div
                        class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg border"
                                :class="
                                    isException(shipment)
                                        ? 'border-destructive/20 bg-destructive/10 text-destructive'
                                        : shipment.ata
                                          ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600'
                                          : 'border-primary/10 bg-primary/10 text-primary'
                                "
                            >
                                <AlertTriangle
                                    v-if="isException(shipment)"
                                    class="size-6"
                                /><Check
                                    v-else-if="shipment.ata"
                                    class="size-6"
                                /><Ship v-else class="size-6" />
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-bold text-primary">
                                        {{ shipment.shipment_number }}
                                    </h2>
                                    <Badge
                                        :variant="
                                            isException(shipment)
                                                ? 'destructive'
                                                : 'outline'
                                        "
                                        >{{
                                            shipment.tracking_status ||
                                            'Belum ada status'
                                        }}</Badge
                                    >
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{
                                        shipment.vessel_name ||
                                        'Kapal belum diisi'
                                    }}
                                    ·
                                    {{
                                        shipment.port_of_loading?.port_name ||
                                        '-'
                                    }}
                                    →
                                    {{
                                        shipment.port_of_discharge?.port_name ||
                                        '-'
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-6 xl:gap-8">
                            <div>
                                <p
                                    class="mb-1 text-[10px] font-bold tracking-widest text-muted-foreground uppercase"
                                >
                                    ETD / ETA
                                </p>
                                <p
                                    class="text-sm font-semibold text-foreground"
                                >
                                    {{ formatDate(shipment.etd) }} →
                                    {{ formatDate(shipment.eta) }}
                                </p>
                            </div>
                            <div class="min-w-[150px]">
                                <div class="mb-1 flex justify-between">
                                    <p
                                        class="text-[10px] font-bold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Progress
                                    </p>
                                    <p class="text-xs font-bold text-primary">
                                        {{ progress(shipment) }}%
                                    </p>
                                </div>
                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-primary transition-all"
                                        :style="{
                                            width: `${progress(shipment)}%`,
                                        }"
                                    />
                                </div>
                            </div>
                            <Link
                                :href="`/shipments/${shipment.id}`"
                                class="rounded-full p-2 transition hover:bg-muted"
                                :aria-label="`Lihat ${shipment.shipment_number}`"
                                ><ExternalLink class="size-5"
                            /></Link>
                        </div>
                    </div>
                </div>
                <div
                    v-if="shipment.milestones?.length"
                    class="overflow-x-auto p-6"
                >
                    <div class="flex min-w-[900px] justify-between">
                        <div
                            v-for="(milestone, index) in shipment.milestones"
                            :key="milestone.id"
                            class="relative flex w-32 shrink-0 flex-col items-center"
                        >
                            <div
                                v-if="index < shipment.milestones.length - 1"
                                class="absolute top-3 left-[calc(50%+14px)] z-0 h-0.5 w-[calc(100%-28px)]"
                                :class="
                                    milestone.status === 'Completed'
                                        ? 'bg-emerald-500'
                                        : 'bg-border'
                                "
                            />
                            <div
                                class="relative z-10 mb-3 flex size-7 items-center justify-center rounded-full border-4 border-card shadow-sm"
                                :class="milestoneClass(milestone.status)"
                            >
                                <Check
                                    v-if="milestone.status === 'Completed'"
                                    class="size-3"
                                /><span v-else class="text-[10px]">{{
                                    milestone.status === 'In Progress'
                                        ? '•'
                                        : ''
                                }}</span>
                            </div>
                            <p
                                class="text-center text-[10px] font-bold uppercase"
                                :class="
                                    milestone.status === 'Completed'
                                        ? 'text-emerald-600'
                                        : milestone.status === 'In Progress'
                                          ? 'text-primary'
                                          : 'text-muted-foreground'
                                "
                            >
                                {{ milestone.milestone_name }}
                            </p>
                            <p
                                class="mt-1 text-center text-[9px] text-muted-foreground"
                            >
                                {{
                                    formatDate(
                                        milestone.actual_date ||
                                            milestone.planned_date,
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    v-else
                    class="border-t border-border px-6 py-4 text-xs text-muted-foreground"
                >
                    Belum ada milestone untuk shipment ini.
                </div>
            </article>
            <div
                v-if="!filteredShipments.length"
                class="rounded-xl border border-dashed p-16 text-center"
            >
                <PackageSearch class="mx-auto size-10 text-muted-foreground" />
                <p class="mt-3 font-semibold">Belum ada data shipment</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Shipment yang dibuat dari menu transaksi akan muncul di
                    sini.
                </p>
            </div>
        </section>
        <div
            class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <p class="text-xs text-muted-foreground">
                Menampilkan {{ filteredShipments.length }} dari
                {{ shipments.length }} shipment.
            </p>
            <div class="relative">
                <Input
                    v-model="search"
                    class="w-full pl-9 sm:w-80"
                    placeholder="Cari shipment, BL, kapal..."
                /><PackageSearch
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
            </div>
        </div>
    </main>
</template>
