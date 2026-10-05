<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ClipboardList,
    ExternalLink,
    Filter,
    Search,
    Truck,
} from 'lucide-vue-next';
import PublicHeader from '@/components/public/PublicHeader.vue';

interface Item {
    id: number;
    quantity: number;
    unit_price: number;
    total_price: number;
}
interface PurchaseOrder {
    id: number;
    po_number: string;
    category: string | null;
    po_date: string;
    delivery_date: string | null;
    arrival_date: string | null;
    status: boolean | string | number | null;
    supplier?: { supplier_name: string; country?: string | null } | null;
    items?: Item[];
}
const props = defineProps<{ purchaseOrders?: PurchaseOrder[] }>();
const search = ref('');
const status = ref('all');
const orders = computed(() => props.purchaseOrders ?? []);
const filteredOrders = computed(() =>
    orders.value.filter((order) => {
        const keyword = search.value.trim().toLowerCase();
        const text = [
            order.po_number,
            order.category,
            order.supplier?.supplier_name,
            order.supplier?.country,
        ]
            .join(' ')
            .toLowerCase();
        const normalized =
            order.status === true || order.status === 1
                ? 'active'
                : order.status === false || order.status === 0
                  ? 'completed'
                  : String(order.status ?? 'active').toLowerCase();
        return (
            (!keyword || text.includes(keyword)) &&
            (status.value === 'all' || normalized === status.value)
        );
    }),
);
const activeOrders = computed(
    () =>
        orders.value.filter(
            (order) => order.status === true || order.status === 1,
        ).length,
);
const arrivedOrders = computed(
    () => orders.value.filter((order) => order.arrival_date).length,
);
const totalItems = computed(() =>
    orders.value.reduce(
        (total, order) => total + (order.items?.length ?? 0),
        0,
    ),
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
function orderStatus(value: PurchaseOrder['status']) {
    return value === true || value === 1
        ? 'Aktif'
        : value === false || value === 0
          ? 'Selesai'
          : String(value || 'Aktif');
}
</script>

<template>
    <main
        class="min-h-screen w-full bg-slate-50 px-4 pt-8 pb-12 text-slate-900 lg:px-8"
    >
        <div class="mx-auto max-w-[1600px]">
            <header
                class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end"
            >
                <div>
                    <nav
                        class="mb-2 flex items-center gap-2 text-xs text-muted-foreground"
                    >
                        <span>Dashboard</span><span>/</span
                        ><span class="font-semibold text-primary"
                            >Tracking PO Lokal</span
                        >
                    </nav>
                    <h1 class="text-3xl font-bold tracking-tight text-primary">
                        Tracking PO Lokal
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Pantau status purchase order lokal dari vendor sampai
                        tujuan.
                    </p>
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-bold shadow-sm hover:bg-muted"
                        @click="status = status === 'all' ? 'active' : 'all'"
                    >
                        <Filter class="size-4" /> Filter</button
                    ><Link
                        href="/po/create"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-primary-foreground shadow-sm hover:opacity-90"
                        ><ClipboardList class="size-4" /> PO Baru</Link
                    >
                </div>
            </header>
            <section
                class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4"
            >
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div
                            class="rounded-lg bg-primary/10 p-2.5 text-primary"
                        >
                            <ClipboardList class="size-5" />
                        </div>
                    </div>
                    <p
                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        Total PO
                    </p>
                    <p class="mt-1 text-2xl font-bold">{{ orders.length }}</p>
                    <p class="mt-2 text-xs text-muted-foreground">
                        Purchase order tercatat
                    </p>
                </div>
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="mb-4 w-fit rounded-lg bg-amber-500/10 p-2.5 text-amber-600"
                    >
                        <Truck class="size-5" />
                    </div>
                    <p
                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        PO Aktif
                    </p>
                    <p class="mt-1 text-2xl font-bold">{{ activeOrders }}</p>
                    <p class="mt-2 text-xs text-muted-foreground">
                        Masih berjalan
                    </p>
                </div>
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="mb-4 w-fit rounded-lg bg-emerald-500/10 p-2.5 text-emerald-600"
                    >
                        <span class="material-symbols-outlined">task_alt</span>
                    </div>
                    <p
                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        Sudah Tiba
                    </p>
                    <p class="mt-1 text-2xl font-bold">{{ arrivedOrders }}</p>
                    <p class="mt-2 text-xs text-muted-foreground">
                        Memiliki tanggal kedatangan
                    </p>
                </div>
                <div
                    class="rounded-xl border border-border bg-primary p-5 text-primary-foreground shadow-lg"
                >
                    <p
                        class="text-[11px] font-semibold tracking-wider uppercase opacity-80"
                    >
                        Detail Item
                    </p>
                    <p class="mt-1 text-3xl font-bold">{{ totalItems }}</p>
                    <p class="mt-2 text-xs opacity-80">Baris item pada PO</p>
                </div>
            </section>
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                <section class="xl:col-span-8">
                    <div
                        class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
                    >
                        <div>
                            <h2 class="text-lg font-bold text-primary">
                                Daftar Purchase Order
                            </h2>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Data aktual dari purchase_orders dan suppliers.
                            </p>
                        </div>
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            /><input
                                v-model="search"
                                class="h-9 w-64 rounded-lg border border-border bg-card pr-3 pl-9 text-xs outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="Cari PO atau vendor..."
                            />
                        </div>
                    </div>
                    <div class="space-y-3">
                        <article
                            v-for="order in filteredOrders"
                            :key="order.id"
                            class="rounded-xl border border-border bg-card p-5 shadow-sm transition hover:shadow-md"
                        >
                            <div
                                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                    >
                                        <ClipboardList class="size-6" />
                                    </div>
                                    <div>
                                        <div
                                            class="flex flex-wrap items-center gap-2"
                                        >
                                            <h3
                                                class="text-lg font-bold text-primary"
                                            >
                                                {{ order.po_number }}
                                            </h3>
                                            <span
                                                class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary uppercase"
                                                >{{
                                                    order.category ||
                                                    'Tanpa kategori'
                                                }}</span
                                            >
                                        </div>
                                        <p
                                            class="mt-1 text-sm text-muted-foreground"
                                        >
                                            {{
                                                order.supplier?.supplier_name ||
                                                'Vendor belum diisi'
                                            }}
                                            ·
                                            {{
                                                order.supplier?.country ||
                                                'Negara belum diisi'
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="grid grid-cols-2 gap-5 border-t pt-4 text-sm md:grid-cols-3 md:border-t-0 md:border-l md:pt-0 md:pl-5"
                                >
                                    <div>
                                        <p
                                            class="text-[10px] tracking-wider text-muted-foreground uppercase"
                                        >
                                            Tanggal PO
                                        </p>
                                        <p class="font-bold">
                                            {{ formatDate(order.po_date) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-[10px] tracking-wider text-muted-foreground uppercase"
                                        >
                                            Tiba
                                        </p>
                                        <p class="font-bold">
                                            {{
                                                formatDate(
                                                    order.arrival_date ||
                                                        order.delivery_date,
                                                )
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
                                            {{ orderStatus(order.status) }}
                                        </p>
                                    </div>
                                </div>
                                <Link
                                    :href="`/po/${order.id}`"
                                    class="self-end rounded-md p-2 text-muted-foreground hover:bg-muted hover:text-primary"
                                    :aria-label="`Lihat ${order.po_number}`"
                                    ><ExternalLink class="size-5"
                                /></Link>
                            </div>
                        </article>
                        <div
                            v-if="!filteredOrders.length"
                            class="rounded-xl border border-dashed bg-card p-16 text-center"
                        >
                            <ClipboardList
                                class="mx-auto size-10 text-muted-foreground"
                            />
                            <p class="mt-3 font-semibold">
                                Belum ada purchase order
                            </p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                PO dari menu transaksi akan muncul di sini.
                            </p>
                        </div>
                    </div>
                </section>
                <aside class="xl:col-span-4">
                    <div
                        class="rounded-xl bg-primary p-6 text-primary-foreground shadow-lg"
                    >
                        <h2 class="text-lg font-bold">Ringkasan PO Lokal</h2>
                        <div class="mt-5 flex flex-col gap-4 text-sm">
                            <div
                                class="flex justify-between border-b border-primary-foreground/20 pb-3"
                            >
                                <span class="opacity-80">Total PO</span
                                ><strong class="text-2xl">{{
                                    orders.length
                                }}</strong>
                            </div>
                            <div
                                class="flex justify-between border-b border-primary-foreground/20 pb-3"
                            >
                                <span class="opacity-80">PO aktif</span
                                ><strong>{{ activeOrders }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="opacity-80">Sudah tiba</span
                                ><strong>{{ arrivedOrders }}</strong>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-6 rounded-xl border border-border bg-card p-5 shadow-sm"
                    >
                        <h2 class="text-lg font-bold">Filter status</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button
                                v-for="item in [
                                    { value: 'all', label: 'Semua' },
                                    { value: 'active', label: 'Aktif' },
                                    { value: 'completed', label: 'Selesai' },
                                ]"
                                :key="item.value"
                                class="rounded-full border px-3 py-1.5 text-xs font-semibold"
                                :class="
                                    status === item.value
                                        ? 'border-primary bg-primary/10 text-primary'
                                        : 'border-border text-muted-foreground'
                                "
                                @click="status = item.value"
                            >
                                {{ item.label }}
                            </button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>
