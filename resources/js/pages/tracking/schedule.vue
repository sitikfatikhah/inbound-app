<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    CalendarDays,
    ChevronLeft,
    ChevronRight,
    ExternalLink,
    Ship,
    Truck,
} from 'lucide-vue-next';
import PublicHeader from '@/components/public/PublicHeader.vue';

interface Shipment {
    id: number;
    shipment_number: string;
    bl_number: string | null;
    vessel_name: string | null;
    voyage_number: string | null;
    eta: string | null;
    etd: string | null;
    tracking_status: string | null;
    status_document: string | null;
    port_of_loading?: { port_name: string } | null;
    port_of_discharge?: { port_name: string } | null;
    warehouse?: { warehouse_name: string } | null;
}

const props = defineProps<{ shipments?: Shipment[] }>();
const search = ref('');
const selectedDate = ref<string | null>(null);
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
                shipment.port_of_discharge?.port_name,
                shipment.warehouse?.warehouse_name,
            ].some((value) =>
                String(value ?? '')
                    .toLowerCase()
                    .includes(keyword),
            );
        return (
            matchesSearch &&
            (!selectedDate.value || shipment.eta === selectedDate.value)
        );
    }),
);
const delayed = computed(
    () =>
        shipments.value.filter((shipment) =>
            /delay|exception/i.test(shipment.tracking_status ?? ''),
        ).length,
);
const inTransit = computed(
    () =>
        shipments.value.filter((shipment) =>
            /transit|shipped|sail/i.test(shipment.tracking_status ?? ''),
        ).length,
);
const monthLabel = computed(() => {
    const date = shipments.value.find((shipment) => shipment.eta)?.eta;
    return date
        ? new Intl.DateTimeFormat('id-ID', {
              month: 'long',
              year: 'numeric',
          }).format(new Date(date))
        : 'Belum ada jadwal';
});
function formatDate(value: string | null) {
    return value
        ? new Intl.DateTimeFormat('id-ID', {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
          }).format(new Date(value))
        : '-';
}
function statusLabel(value: string | null) {
    return value || 'Belum ada status';
}
</script>

<template>
    <main class="min-h-screen w-full bg-slate-50 px-4 pt-8 pb-12 text-slate-900 lg:px-8">
        <section
            class="mb-8 flex flex-col items-start justify-between gap-4 md:flex-row md:items-end"
        >
            <div>
                <nav
                    class="mb-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <span>Dashboard</span><span>/</span
                    ><span class="font-semibold text-primary"
                        >Jadwal Kedatangan</span
                    >
                </nav>
                <h1 class="text-3xl font-bold tracking-tight text-primary">
                    Jadwal Kedatangan
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Monitoring real-time kedatangan shipment Import dari data
                    operasional.
                </p>
            </div>
            <div class="flex gap-2">
                <Link
                    href="/tracking/import"
                    class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-bold text-foreground transition hover:bg-muted"
                    ><Ship class="size-4" /> Tracking Import</Link
                ><Link
                    href="/tracking/warehouse"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-primary-foreground shadow-sm transition hover:opacity-90"
                    ><Truck class="size-4" /> Warehouse</Link
                >
            </div>
        </section>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 flex flex-col gap-6 lg:col-span-8">
                <div
                    class="bg-surface relative h-[360px] overflow-hidden rounded-xl border border-border shadow-sm"
                >
                    <img
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDHPq9KRYr-zcJc23TmfE5_ERo3t2iXVfN1S8g52noVqGewqyYGzQLycZwk5sEKpjgZPvnEsGsCLdIWC4Abjr_WmX0TawdGvyg8CSgxtOtkg5w0sZlLYbRvxIqEW0CBuq1rdiNmarM6RVbAzLoXdqE_sE4E17IguINpB5OTmsvJmE7nwdiHGtqGe9GcpXqOyNvWkYx05F5aYuIt_Im2x9OR5w8EA5C0R0TQHTUlx8f81QF74uAQ4lRtvRdmZefpTUom0Qr5eU0kCs7"
                        alt="Peta rute shipment"
                        class="h-full w-full object-cover opacity-60 grayscale-[0.35]"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-background/80"
                    />
                    <div
                        class="absolute top-4 left-4 rounded-lg border border-white/60 bg-white/80 p-3 shadow-lg backdrop-blur"
                    >
                        <h2 class="text-sm font-bold text-primary">
                            Status Armada Live
                        </h2>
                        <div class="mt-2 flex gap-4 text-xs">
                            <span class="flex items-center gap-1.5"
                                ><span
                                    class="size-2 rounded-full bg-emerald-500"
                                />{{ inTransit }} In Transit</span
                            ><span class="flex items-center gap-1.5"
                                ><span
                                    class="size-2 rounded-full bg-destructive"
                                />{{ delayed }} Delay</span
                            >
                        </div>
                    </div>
                    <div
                        class="absolute right-5 bottom-5 rounded-lg bg-card/90 px-4 py-3 text-right shadow-lg backdrop-blur"
                    >
                        <p class="text-xs text-muted-foreground">
                            Shipment terjadwal
                        </p>
                        <p class="text-2xl font-bold text-primary">
                            {{ shipments.length }}
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-primary">
                            Jadwal {{ monthLabel }}
                        </h2>
                        <div class="flex items-center gap-2">
                            <button
                                class="rounded-md p-1.5 hover:bg-muted"
                                aria-label="Bulan sebelumnya"
                            >
                                <ChevronLeft class="size-4" /></button
                            ><button
                                class="rounded-md p-1.5 hover:bg-muted"
                                aria-label="Bulan berikutnya"
                            >
                                <ChevronRight class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            class="rounded-lg border border-border p-3 text-center text-xs transition hover:border-primary"
                            :class="{
                                'border-primary bg-primary/5 text-primary':
                                    !selectedDate,
                            }"
                            @click="selectedDate = null"
                        >
                            <span class="block font-semibold">SEMUA</span
                            ><span class="mt-1 block text-xl font-bold">{{
                                shipments.length
                            }}</span></button
                        ><input
                            v-model="selectedDate"
                            type="date"
                            class="h-12 rounded-lg border border-border bg-background px-3 text-sm"
                            aria-label="Pilih tanggal ETA"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <div
                        v-for="shipment in filteredShipments"
                        :key="shipment.id"
                        class="group rounded-xl border border-border bg-card p-5 shadow-sm transition hover:shadow-md"
                        :class="{
                            'border-l-4 border-l-destructive':
                                /delay|exception/i.test(
                                    shipment.tracking_status ?? '',
                                ),
                        }"
                    >
                        <div
                            class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                >
                                    <Ship class="size-6" />
                                </div>
                                <div>
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <h3
                                            class="text-lg font-bold text-foreground"
                                        >
                                            {{ shipment.shipment_number }}
                                        </h3>
                                        <span
                                            class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary uppercase"
                                            >{{
                                                shipment.status_document ||
                                                'Shipment'
                                            }}</span
                                        >
                                    </div>
                                    <p
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        {{
                                            shipment.vessel_name ||
                                            'Kapal belum diisi'
                                        }}
                                        ·
                                        {{
                                            shipment.port_of_loading
                                                ?.port_name || '-'
                                        }}
                                        →
                                        {{
                                            shipment.port_of_discharge
                                                ?.port_name || '-'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="grid w-full grid-cols-2 gap-4 border-t border-border pt-4 text-sm md:w-auto md:grid-cols-3 md:border-t-0 md:border-l md:pt-0 md:pl-5"
                            >
                                <div>
                                    <p
                                        class="text-[10px] tracking-wider text-muted-foreground uppercase"
                                    >
                                        ETA
                                    </p>
                                    <p class="font-bold">
                                        {{ formatDate(shipment.eta) }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] tracking-wider text-muted-foreground uppercase"
                                    >
                                        Gudang
                                    </p>
                                    <p class="font-bold">
                                        {{
                                            shipment.warehouse
                                                ?.warehouse_name || '-'
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] tracking-wider text-muted-foreground uppercase"
                                    >
                                        Status
                                    </p>
                                    <p class="font-bold text-primary">
                                        {{
                                            statusLabel(
                                                shipment.tracking_status,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                            <Link
                                :href="`/shipments/${shipment.id}`"
                                class="self-end rounded-md p-2 text-muted-foreground transition hover:bg-muted hover:text-primary"
                                :aria-label="`Buka ${shipment.shipment_number}`"
                                ><ExternalLink class="size-5"
                            /></Link>
                        </div>
                    </div>
                    <div
                        v-if="!filteredShipments.length"
                        class="rounded-xl border border-dashed p-12 text-center"
                    >
                        <CalendarDays
                            class="mx-auto size-10 text-muted-foreground"
                        />
                        <p class="mt-3 font-semibold">
                            Belum ada jadwal kedatangan
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Shipment dengan ETA akan muncul di area ini.
                        </p>
                    </div>
                </div>
            </div>

            <aside class="col-span-12 flex flex-col gap-6 lg:col-span-4">
                <div
                    class="rounded-xl bg-primary p-6 text-primary-foreground shadow-lg"
                >
                    <h2 class="text-lg font-bold">Ringkasan Kedatangan</h2>
                    <div class="mt-5 flex flex-col gap-4 text-sm">
                        <div
                            class="flex items-center justify-between border-b border-primary-foreground/20 pb-3"
                        >
                            <span class="opacity-80">Total shipment</span
                            ><strong class="text-2xl">{{
                                shipments.length
                            }}</strong>
                        </div>
                        <div
                            class="flex items-center justify-between border-b border-primary-foreground/20 pb-3"
                        >
                            <span class="opacity-80">ETA tersedia</span
                            ><strong>{{
                                shipments.filter((shipment) => shipment.eta)
                                    .length
                            }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Terjadwal hari ini</span
                            ><strong>{{
                                shipments.filter(
                                    (shipment) =>
                                        shipment.eta ===
                                        new Date().toISOString().slice(0, 10),
                                ).length
                            }}</strong>
                        </div>
                    </div>
                </div>
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <h2 class="text-lg font-bold text-foreground">
                        Cari jadwal
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Nomor shipment, kapal, port, atau gudang.
                    </p>
                    <input
                        v-model="search"
                        class="mt-4 h-10 w-full rounded-lg border border-border bg-background px-3 text-sm ring-primary/20 outline-none focus:ring-2"
                        placeholder="Cari data..."
                    />
                </div>
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <h2 class="text-lg font-bold text-foreground">
                        Informasi rute
                    </h2>
                    <div
                        class="mt-4 flex flex-col gap-3 text-sm text-muted-foreground"
                    >
                        <p class="flex items-center gap-2">
                            <Ship class="size-4 text-primary" /> Data bersumber
                            dari shipment
                        </p>
                        <p class="flex items-center gap-2">
                            <Truck class="size-4 text-primary" /> Tujuan
                            warehouse terhubung
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</template>
