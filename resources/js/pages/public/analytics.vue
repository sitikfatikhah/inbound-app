<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    Activity,
    BarChart3,
    CalendarDays,
    PackageCheck,
    Ship,
    TrendingUp,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface PurchaseOrder {
    id: number;
    category: string | null;
    po_date: string;
    arrival_date: string | null;
    status: boolean | string | number | null;
}
interface Shipment {
    id: number;
    eta: string | null;
    ata: string | null;
    tracking_status: string | null;
}
const props = defineProps<{
    purchaseOrders?: PurchaseOrder[];
    shipments?: Shipment[];
}>();
const period = ref('all');
const orders = computed(() => props.purchaseOrders ?? []);
const shipments = computed(() => props.shipments ?? []);
const arrivedOrders = computed(() =>
    orders.value.filter((order) => order.arrival_date),
);
const activeShipments = computed(() =>
    shipments.value.filter((shipment) => !shipment.ata),
);
const completionRate = computed(() =>
    orders.value.length
        ? Math.round((arrivedOrders.value.length / orders.value.length) * 100)
        : 0,
);
const categorySummary = computed(() =>
    [
        ...new Set(
            orders.value.map(
                (order) => order.category || 'Tidak dikategorikan',
            ),
        ),
    ].map((category) => ({
        category,
        count: orders.value.filter(
            (order) => (order.category || 'Tidak dikategorikan') === category,
        ).length,
    })),
);
const statusSummary = computed(() =>
    [
        ...new Set(
            shipments.value.map(
                (shipment) => shipment.tracking_status || 'Belum ada status',
            ),
        ),
    ].map((status) => ({
        status,
        count: shipments.value.filter(
            (shipment) =>
                (shipment.tracking_status || 'Belum ada status') === status,
        ).length,
    })),
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
</script>

<template>
    <main
        class="min-h-screen w-full bg-slate-50 px-4 pt-8 pb-12 text-slate-900 lg:px-8"
    >
        <div class="mx-auto max-w-[1600px] space-y-8">
            <section
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div
                    class="flex flex-col justify-between gap-4 xl:flex-row xl:items-end"
                >
                    <div>
                        <p
                            class="mb-1 text-xs font-semibold tracking-[0.12em] text-primary uppercase"
                        >
                            Supply Chain Control Tower
                        </p>
                        <h1
                            class="text-3xl font-bold tracking-tight text-primary"
                        >
                            Logistics Performance
                        </h1>
                        <p
                            class="mt-1 max-w-2xl text-sm leading-6 text-slate-500"
                        >
                            Monitor performa purchase order, kedatangan, dan
                            shipment berdasarkan data operasional aktual.
                        </p>
                    </div>
                    <select
                        v-model="period"
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm"
                    >
                        <option value="all">Semua periode</option>
                        <option value="month">Bulan ini</option>
                        <option value="year">Tahun ini</option>
                    </select>
                </div>
            </section>
            <section
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
            >
                <div
                    v-for="item in [
                        {
                            label: 'Total Purchase Order',
                            value: orders.length,
                            icon: Activity,
                            tone: 'text-primary bg-primary/5',
                        },
                        {
                            label: 'Shipment Aktif',
                            value: activeShipments.length,
                            icon: Ship,
                            tone: 'text-blue-600 bg-blue-50',
                        },
                        {
                            label: 'PO Sudah Tiba',
                            value: arrivedOrders.length,
                            icon: PackageCheck,
                            tone: 'text-emerald-600 bg-emerald-50',
                        },
                        {
                            label: 'Arrival Rate',
                            value: `${completionRate}%`,
                            icon: TrendingUp,
                            tone: 'text-amber-600 bg-amber-50',
                        },
                    ]"
                    :key="item.label"
                    class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500">
                                {{ item.label }}
                            </p>
                            <p class="mt-3 text-2xl font-bold text-primary">
                                {{ item.value }}
                            </p>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-lg"
                            :class="item.tone"
                        >
                            <component :is="item.icon" class="size-5" />
                        </div>
                    </div>
                    <div
                        v-if="item.label === 'Arrival Rate'"
                        class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"
                    >
                        <div
                            class="h-full rounded-full bg-primary transition-all"
                            :style="{ width: `${completionRate}%` }"
                        />
                    </div>
                </div>
            </section>
            <section class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                <Card class="xl:col-span-8"
                    ><CardHeader
                        ><div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Performa operasional</CardTitle
                                ><CardDescription
                                    >Distribusi data purchase order dan shipment
                                    yang tersedia.</CardDescription
                                >
                            </div>
                            <BarChart3
                                class="size-5 text-primary"
                            /></div></CardHeader
                    ><CardContent
                        ><div
                            v-if="categorySummary.length"
                            class="flex h-64 items-end justify-around gap-4 border-b border-slate-200 px-4 pt-8 pb-0"
                        >
                            <div
                                v-for="item in categorySummary"
                                :key="item.category"
                                class="flex h-full flex-1 flex-col items-center justify-end gap-2"
                            >
                                <div
                                    class="w-full max-w-16 rounded-t-lg bg-primary/70 transition-all"
                                    :style="{
                                        height: `${Math.max((item.count / Math.max(orders.length, 1)) * 100, 12)}%`,
                                    }"
                                />
                                <span
                                    class="max-w-24 text-center text-[10px] text-slate-500"
                                    >{{ item.category }}</span
                                ><strong class="text-sm text-primary">{{
                                    item.count
                                }}</strong>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex h-64 items-center justify-center text-sm text-muted-foreground"
                        >
                            Belum ada data purchase order.
                        </div></CardContent
                    ></Card
                ><Card class="xl:col-span-4"
                    ><CardHeader
                        ><CardTitle>Status Shipment</CardTitle
                        ><CardDescription
                            >Status tracking terakhir.</CardDescription
                        ></CardHeader
                    ><CardContent
                        ><div
                            v-if="statusSummary.length"
                            class="flex flex-col gap-4"
                        >
                            <div
                                v-for="item in statusSummary"
                                :key="item.status"
                                class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-0"
                            >
                                <span class="text-sm">{{ item.status }}</span
                                ><Badge variant="secondary">{{
                                    item.count
                                }}</Badge>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">
                            Belum ada shipment.
                        </p></CardContent
                    ></Card
                >
            </section>
            <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <Card
                    ><CardHeader
                        ><CardTitle>Efisiensi kedatangan</CardTitle
                        ><CardDescription
                            >Perbandingan PO tiba terhadap seluruh
                            PO.</CardDescription
                        ></CardHeader
                    ><CardContent
                        ><div class="flex items-center gap-6">
                            <div
                                class="relative flex size-40 items-center justify-center rounded-full"
                                :style="{
                                    background: `conic-gradient(#00355f ${completionRate}%, #e2e8f0 0)`,
                                }"
                            >
                                <div
                                    class="flex size-28 flex-col items-center justify-center rounded-full bg-white"
                                >
                                    <span class="text-xs text-slate-400"
                                        >Arrival</span
                                    ><strong class="text-2xl text-primary"
                                        >{{ completionRate }}%</strong
                                    >
                                </div>
                            </div>
                            <div class="flex flex-col gap-3 text-sm">
                                <p>
                                    <strong>{{ arrivedOrders.length }}</strong>
                                    PO sudah tiba
                                </p>
                                <p>
                                    <strong>{{
                                        orders.length - arrivedOrders.length
                                    }}</strong>
                                    PO belum tiba
                                </p>
                                <p class="text-muted-foreground">
                                    Berdasarkan tanggal arrival_date.
                                </p>
                            </div>
                        </div></CardContent
                    ></Card
                ><Card
                    ><CardHeader
                        ><CardTitle>Aktivitas terbaru</CardTitle
                        ><CardDescription
                            >Shipment terakhir yang tercatat.</CardDescription
                        ></CardHeader
                    ><CardContent
                        ><div
                            v-if="shipments.length"
                            class="flex flex-col gap-3"
                        >
                            <div
                                v-for="shipment in shipments.slice(0, 5)"
                                :key="shipment.id"
                                class="flex items-center justify-between border-b pb-3 last:border-0"
                            >
                                <div>
                                    <p class="text-sm font-medium">
                                        Shipment #{{ shipment.id }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        ETA {{ formatDate(shipment.eta) }}
                                    </p>
                                </div>
                                <Badge variant="outline">{{
                                    shipment.tracking_status ||
                                    'Belum ada status'
                                }}</Badge>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">
                            Belum ada aktivitas shipment.
                        </p></CardContent
                    ></Card
                >
            </section>
            <Card
                ><CardHeader
                    ><CardTitle>Purchase order terbaru</CardTitle
                    ><CardDescription
                        >Data yang digunakan dalam ringkasan
                        analytics.</CardDescription
                    ></CardHeader
                ><CardContent
                    ><div v-if="orders.length" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b bg-muted/40">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Kategori</th>
                                    <th class="px-4 py-3">Tanggal PO</th>
                                    <th class="px-4 py-3">Tanggal tiba</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in orders.slice(0, 10)"
                                    :key="order.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="px-4 py-3 font-semibold">
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ order.category || '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ formatDate(order.po_date) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ formatDate(order.arrival_date) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else
                        class="flex flex-col items-center gap-3 p-10 text-center"
                    >
                        <CalendarDays class="size-10 text-muted-foreground" />
                        <p class="font-medium">Belum ada data analytics</p>
                    </div></CardContent
                ></Card
            >
        </div>
    </main>
</template>
