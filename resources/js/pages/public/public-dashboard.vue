<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

import {
    AlertTriangle,
    Bell,
    CalendarDays,
    ChevronRight,
    CircleHelp,
    Clock3,
    ExternalLink,
    FileWarning,
    Filter,
    Gavel,
    Import,
    Package,
    PackageCheck,
    RefreshCw,
    Search,
    Ship,
    TrendingUp,
    Truck,
} from 'lucide-vue-next';

type ChartMode = 'weekly' | 'monthly';

interface KpiItem {
    label: string;
    value: number;
    icon: any;
    iconClass: string;
    iconBackground: string;
    trend?: string;
    trendClass?: string;
    badge?: string;
    badgeClass?: string;
}

interface ExceptionItem {
    id: string;
    reference: string;
    product: string;
    vendor: string;
    status: string;
    statusClass: string;
    eta: string;
    action: string;
}

const page = usePage();

const searchQuery = ref('');
const chartMode = ref<ChartMode>('weekly');

const currentUser = computed(() => page.props.auth?.user ?? null);

const userName = computed(() => {
    return currentUser.value?.name ?? 'Pengguna';
});

const userInitial = computed(() => {
    return userName.value.charAt(0).toUpperCase();
});

/**
 * KPI Dashboard
 *
 * Untuk tahap berikutnya data ini dapat diganti
 * dengan props dari Controller Laravel.
 */
const kpis: KpiItem[] = [
    {
        label: 'Import Sedang Berjalan',
        value: 24,
        icon: Import,
        iconClass: 'text-[#00355f]',
        iconBackground: 'bg-blue-50',
        trend: '12%',
        trendClass: 'text-emerald-600 bg-emerald-50',
    },
    {
        label: 'PO Lokal Berjalan',
        value: 158,
        icon: Package,
        iconClass: 'text-[#005faf]',
        iconBackground: 'bg-sky-50',
        trend: '0%',
        trendClass: 'text-amber-600 bg-amber-50',
    },
    {
        label: 'Tiba Hari Ini',
        value: 12,
        icon: CalendarDays,
        iconClass: 'text-[#00355f]',
        iconBackground: 'bg-slate-100',
    },
    {
        label: 'Tiba Minggu Ini',
        value: 45,
        icon: CalendarDays,
        iconClass: 'text-[#00355f]',
        iconBackground: 'bg-slate-100',
    },
    {
        label: 'Terlambat',
        value: 8,
        icon: AlertTriangle,
        iconClass: 'text-red-600',
        iconBackground: 'bg-red-50',
        badge: 'Urgent',
        badgeClass: 'text-red-700 bg-red-50',
    },
    {
        label: 'Menunggu Clearance',
        value: 19,
        icon: Gavel,
        iconClass: 'text-orange-700',
        iconBackground: 'bg-orange-50',
    },
    {
        label: 'Belum Diterima',
        value: 31,
        icon: PackageCheck,
        iconClass: 'text-amber-600',
        iconBackground: 'bg-amber-50',
    },
];

/**
 * Chart data.
 *
 * Masih menggunakan data contoh dari desain awal.
 * Nanti dapat diganti dengan data Laravel.
 */
const weeklyChart = [
    { label: 'Senin', value: 42 },
    { label: 'Selasa', value: 30 },
    { label: 'Rabu', value: 58 },
    { label: 'Kamis', value: 38 },
    { label: 'Jumat', value: 46 },
    { label: 'Sabtu', value: 15 },
    { label: 'Minggu', value: 12 },
];

const monthlyChart = [
    { label: 'M1', value: 128 },
    { label: 'M2', value: 156 },
    { label: 'M3', value: 142 },
    { label: 'M4', value: 178 },
];

const chartData = computed(() => {
    return chartMode.value === 'weekly'
        ? weeklyChart
        : monthlyChart;
});

const maxChartValue = computed(() => {
    return Math.max(...chartData.value.map((item) => item.value));
});

const exceptions: ExceptionItem[] = [
    {
        id: 'IMP-2024-8891',
        reference: 'Bill of Lading: MSKU22910',
        product: 'Stainless Steel Coil 304',
        vendor: 'Posco International',
        status: 'Customs Hold',
        statusClass: 'bg-red-50 text-red-700 border-red-100',
        eta: '24 Mei 2024',
        action: 'Selesaikan',
    },
    {
        id: 'PO-L-55291',
        reference: 'Vendor Invoice: INV/2024/002',
        product: 'Industrial Bearings AX-12',
        vendor: 'PT. Teknik Mandiri',
        status: 'Late Departure',
        statusClass: 'bg-amber-50 text-amber-700 border-amber-100',
        eta: '26 Mei 2024',
        action: 'Hubungi Vendor',
    },
    {
        id: 'IMP-2024-8902',
        reference: 'Vessel: Ever Given II',
        product: 'Electronic Components Bulk',
        vendor: 'Samsung SDI',
        status: 'Port Congestion',
        statusClass: 'bg-sky-50 text-sky-700 border-sky-100',
        eta: '27 Mei 2024',
        action: 'Update ETA',
    },
];

const monitoringStatus = [
    {
        label: 'Lancar',
        value: 182,
        dotClass: 'bg-emerald-500',
    },
    {
        label: 'Peringatan',
        value: 12,
        dotClass: 'bg-amber-500',
    },
    {
        label: 'Keterlambatan',
        value: 8,
        dotClass: 'bg-red-500',
    },
];

function setChartMode(mode: ChartMode) {
    chartMode.value = mode;
}


</script>

<template>
    <main class="min-h-screen bg-slate-50">
        <!-- =========================================================
             TOP NAVIGATION
        ========================================================== -->
        <header
            class="fixed left-72 right-0 top-0 z-40 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-8 backdrop-blur-md"
        >
            <!-- Search -->
            <form
                class="w-full max-w-xl"
                @submit.prevent="handleSearch"
            >
                <div class="group relative">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-[#00355f]"
                    />

                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Cari shipment, PO, produk, atau vendor..."
                        class="h-10 w-full rounded-full border-0 bg-slate-100 pl-10 pr-4 text-sm text-slate-800 outline-none ring-0 transition-all placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-[#00355f]/15"
                    />
                </div>
            </form>

            <!-- Right Navigation -->
            <div class="ml-6 flex shrink-0 items-center gap-5">
                <!-- Connection Status -->
                <div
                    class="hidden items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 xl:flex"
                >
                    <span
                        class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"
                    />

                    <span class="text-xs font-semibold text-emerald-700">
                        Dynamics 365 Terhubung
                    </span>
                </div>

                <!-- Notifications -->
                <button
                    type="button"
                    aria-label="Notifikasi"
                    class="relative rounded-full p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-[#00355f]"
                >
                    <Bell class="h-5 w-5" />

                    <span
                        class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"
                    />
                </button>

                <div class="h-8 w-px bg-slate-200" />

                <!-- User -->
                <div class="flex items-center gap-3">
                    <div class="hidden text-right lg:block">
                        <p
                            class="max-w-40 truncate text-sm font-semibold text-slate-800"
                        >
                            {{ userName }}
                        </p>

                        <p class="text-xs text-slate-500">
                            Supply Chain
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-100 bg-blue-50 text-sm font-bold text-[#00355f]"
                    >
                        {{ userInitial }}
                    </div>
                </div>
            </div>
        </header>

        <!-- =========================================================
             PAGE CONTENT
        ========================================================== -->
        <div class="mx-auto max-w-[1600px] px-8 pb-12 pt-24">
            <!-- Page Heading -->
            <section
                class="mb-8 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between"
            >
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        />

                        <span
                            class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                        >
                            Operational Control Tower
                        </span>
                    </div>

                    <h1
                        class="font-['Hanken_Grotesk'] text-3xl font-semibold tracking-tight text-slate-900"
                    >
                        Pusat Kendali Operasional
                    </h1>

                    <p
                        class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500"
                    >
                        <RefreshCw class="h-4 w-4" />

                        Sinkronisasi terakhir dengan Dynamics 365:

                        <span class="font-semibold text-slate-700">
                            6 menit yang lalu
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition-all hover:border-[#00355f] hover:text-[#00355f]"
                    >
                        <Filter class="h-4 w-4" />
                        Filter Global
                    </button>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#00355f] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-[#0f4c81]"
                    >
                        <Package class="h-4 w-4" />
                        Input Shipment Baru
                    </button>
                </div>
            </section>

            <!-- =====================================================
                 KPI
            ====================================================== -->
            <section
                class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7"
            >
                <article
                    v-for="kpi in kpis"
                    :key="kpi.label"
                    class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg"
                            :class="kpi.iconBackground"
                        >
                            <component
                                :is="kpi.icon"
                                class="h-5 w-5"
                                :class="kpi.iconClass"
                            />
                        </div>

                        <span
                            v-if="kpi.trend"
                            class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-[11px] font-bold"
                            :class="kpi.trendClass"
                        >
                            <TrendingUp
                                v-if="kpi.trend !== '0%'"
                                class="h-3 w-3"
                            />

                            <span v-else>—</span>

                            {{ kpi.trend }}
                        </span>

                        <span
                            v-if="kpi.badge"
                            class="rounded-full px-2 py-1 text-[11px] font-bold"
                            :class="kpi.badgeClass"
                        >
                            {{ kpi.badge }}
                        </span>
                    </div>

                    <p
                        class="truncate text-xs font-semibold text-slate-500"
                    >
                        {{ kpi.label }}
                    </p>

                    <p
                        class="mt-1 font-['Hanken_Grotesk'] text-2xl font-semibold text-slate-900"
                    >
                        {{ String(kpi.value).padStart(2, '0') }}
                    </p>
                </article>
            </section>

            <!-- =====================================================
                 MAIN DASHBOARD
            ====================================================== -->
            <section class="grid grid-cols-1 gap-8 xl:grid-cols-3">
                <!-- Shipment Timeline -->
                <article
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2"
                >
                    <div
                        class="flex flex-col gap-4 border-b border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#00355f]"
                            >
                                <Clock3 class="h-5 w-5" />
                            </div>

                            <div>
                                <h2
                                    class="text-base font-semibold text-slate-900"
                                >
                                    Insight Timeline Shipment Berjalan
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Perbandingan aktivitas kedatangan shipment
                                </p>
                            </div>
                        </div>

                        <!-- Chart Toggle -->
                        <div
                            class="flex w-fit rounded-lg bg-slate-100 p-1"
                        >
                            <button
                                type="button"
                                class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all"
                                :class="
                                    chartMode === 'weekly'
                                        ? 'bg-white text-[#00355f] shadow-sm'
                                        : 'text-slate-500 hover:text-slate-800'
                                "
                                @click="setChartMode('weekly')"
                            >
                                Mingguan
                            </button>

                            <button
                                type="button"
                                class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all"
                                :class="
                                    chartMode === 'monthly'
                                        ? 'bg-white text-[#00355f] shadow-sm'
                                        : 'text-slate-500 hover:text-slate-800'
                                "
                                @click="setChartMode('monthly')"
                            >
                                Bulanan
                            </button>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="p-6 sm:p-8">
                        <div
                            class="flex h-[320px] items-end gap-3 sm:gap-5"
                        >
                            <div
                                v-for="item in chartData"
                                :key="item.label"
                                class="group flex h-full flex-1 flex-col justify-end"
                            >
                                <div
                                    class="relative flex flex-1 items-end"
                                >
                                    <div
                                        class="relative w-full rounded-t-lg bg-blue-100 transition-all duration-300 group-hover:bg-[#00355f]"
                                        :style="{
                                            height: `${Math.max(
                                                (item.value /
                                                    maxChartValue) *
                                                    85,
                                                8,
                                            )}%`,
                                        }"
                                    >
                                        <div
                                            class="absolute -top-9 left-1/2 -translate-x-1/2 rounded-md bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100"
                                        >
                                            {{ item.value }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 text-center text-[11px] font-semibold text-slate-500"
                                >
                                    {{ item.label }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap gap-6 border-t border-slate-100 px-8 py-5"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="h-3 w-3 rounded-full bg-blue-100"
                            />

                            <span class="text-xs text-slate-500">
                                Target Kedatangan
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                class="h-3 w-3 rounded-full bg-[#00355f]"
                            />

                            <span class="text-xs text-slate-500">
                                Realisasi Kedatangan
                            </span>
                        </div>
                    </div>
                </article>

                <!-- Global Monitoring -->
                <article
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-200 p-6"
                    >
                        <div>
                            <h2
                                class="text-base font-semibold text-slate-900"
                            >
                                Global Monitoring
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Status pengiriman aktif
                            </p>
                        </div>

                        <button
                            type="button"
                            aria-label="Buka monitoring"
                            class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-[#00355f]"
                        >
                            <ExternalLink class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Map Area -->
                    <div
                        class="relative min-h-[360px] overflow-hidden bg-slate-100"
                    >
                        <!-- Simple map background -->
                        <div class="absolute inset-0 opacity-40">
                            <div
                                class="absolute left-[20%] top-[25%] h-24 w-40 rotate-12 rounded-[45%] bg-slate-300"
                            />

                            <div
                                class="absolute left-[42%] top-[42%] h-32 w-20 -rotate-12 rounded-[45%] bg-slate-300"
                            />

                            <div
                                class="absolute right-[12%] top-[25%] h-20 w-32 rotate-6 rounded-[45%] bg-slate-300"
                            />

                            <div
                                class="absolute bottom-[25%] left-[30%] h-16 w-28 -rotate-12 rounded-[50%] bg-slate-300"
                            />

                            <div
                                class="absolute bottom-[12%] right-[20%] h-28 w-16 rotate-12 rounded-[50%] bg-slate-300"
                            />
                        </div>

                        <!-- Shipment Marker -->
                        <div
                            class="group absolute left-[35%] top-[35%] cursor-pointer"
                        >
                            <div
                                class="relative flex h-4 w-4 items-center justify-center rounded-full bg-[#00355f]"
                            >
                                <span
                                    class="absolute inset-0 animate-ping rounded-full bg-[#00355f] opacity-50"
                                />
                            </div>

                            <div
                                class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 shadow-xl group-hover:block"
                            >
                                MV Jakarta Star — Dalam Perjalanan
                            </div>
                        </div>

                        <!-- Delay Marker -->
                        <div
                            class="group absolute bottom-[35%] right-[25%] cursor-pointer"
                        >
                            <div
                                class="relative flex h-4 w-4 items-center justify-center rounded-full bg-red-500"
                            >
                                <span
                                    class="absolute inset-0 animate-ping rounded-full bg-red-500 opacity-50"
                                />
                            </div>

                            <div
                                class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 shadow-xl group-hover:block"
                            >
                                TRK-8829 — Terlambat
                            </div>
                        </div>

                        <!-- Status Summary -->
                        <div
                            class="absolute bottom-4 left-4 right-4 rounded-xl border border-white/60 bg-white/90 p-4 shadow-lg backdrop-blur-sm"
                        >
                            <p
                                class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                            >
                                Status Pengiriman Aktif
                            </p>

                            <div class="space-y-2.5">
                                <div
                                    v-for="status in monitoringStatus"
                                    :key="status.label"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="h-2 w-2 rounded-full"
                                            :class="status.dotClass"
                                        />

                                        <span
                                            class="text-xs text-slate-500"
                                        >
                                            {{ status.label }}
                                        </span>
                                    </div>

                                    <span
                                        class="text-xs font-bold text-slate-800"
                                    >
                                        {{ status.value }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <!-- =====================================================
                 EXCEPTION TABLE
            ====================================================== -->
            <section
                class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex flex-col gap-3 border-b border-slate-200 px-8 py-6 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600"
                        >
                            <FileWarning class="h-5 w-5" />
                        </div>

                        <div>
                            <h2
                                class="text-base font-semibold text-slate-900"
                            >
                                Aksi Diperlukan
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Keterlambatan dan isu shipment yang membutuhkan
                                tindak lanjut
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/control-tower/issues"
                        class="inline-flex items-center gap-1 text-xs font-bold text-[#00355f] hover:underline"
                    >
                        Lihat Semua Isu
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50"
                            >
                                <th
                                    class="px-8 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    ID Shipment / PO
                                </th>

                                <th
                                    class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Nama Produk
                                </th>

                                <th
                                    class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Vendor
                                </th>

                                <th
                                    class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    ETA Semula
                                </th>

                                <th
                                    class="px-8 py-4 text-right text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="item in exceptions"
                                :key="item.id"
                                class="transition-colors hover:bg-slate-50"
                            >
                                <td class="px-8 py-4">
                                    <p
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        {{ item.id }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-[10px] text-slate-400"
                                    >
                                        {{ item.reference }}
                                    </p>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-700"
                                >
                                    {{ item.product }}
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-700"
                                >
                                    {{ item.vendor }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full border px-3 py-1 text-[10px] font-bold"
                                        :class="item.statusClass"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-500"
                                >
                                    {{ item.eta }}
                                </td>

                                <td class="px-8 py-4 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg border border-[#00355f] px-3 py-1.5 text-xs font-bold text-[#00355f] transition-colors hover:bg-blue-50"
                                    >
                                        {{ item.action }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Footer Information -->
            <footer
                class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-slate-200 pt-6 text-xs text-slate-400 sm:flex-row"
            >
                <p>
                    PT. CSA Supply Chain Control Tower
                </p>

                <div class="flex items-center gap-2">
                    <CircleHelp class="h-4 w-4" />

                    <span>
                        Sistem monitoring operasional dan import tracking
                    </span>
                </div>
            </footer>
        </div>
    </main>
</template>
