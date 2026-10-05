<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
    Bell,
    ChevronDown,
    HelpCircle,
    Search,
    Ship,
    Truck,
    Plane,
    Package,
    Check,
    AlertTriangle,
    MoreVertical,
    Clock3,
    ArrowRight,
    Lock,
    Globe,
    Mail,
    FileText,
} from 'lucide-vue-next'

import Input from '@/components/Input.vue'
import { Button } from '@/components/ui/button'

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

type Supplier = {
    id?: number
    supplier_name?: string
    country?: string
}

type Shipment = {
    id: number
    forwarder_id?: number | string
    shipping_line_id?: number | string
    port_of_loading_id?: number | string
    port_of_discharge_id?: number | string
    warehouse_id?: number | string

    bl_number?: string
    vessel_name?: string
    voyage_number?: string

    etd?: string | null
    eta?: string | null
    etd_actual?: string | null
    eta_actual?: string | null
    ata?: string | null
    shipment_date?: string | null

    status?: string
    tracking_status?: string

    supplier?: Supplier
}

type ShipmentActivity = {
    current?: string
    date?: string
    location?: string
    remarks?: string
}

type KnowledgeItem = {
    title: string
    category: string
    description: string
    image: string
}

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
|
| Semua props dibuat optional supaya halaman tidak menghasilkan
| warning "Missing required prop".
|
*/

const props = withDefaults(
    defineProps<{
        shipments?: Shipment[]
        activeShipments?: number
        vesselsAtSea?: number
        portResolutionsToday?: number

        activity?: ShipmentActivity

        supplier?: Supplier

        today?: string

        currentMonth?: string

        knowledgeItems?: KnowledgeItem[]
    }>(),
    {
        shipments: () => [],
        activeShipments: 0,
        vesselsAtSea: 0,
        portResolutionsToday: 0,

        activity: () => ({
            current: 'Dalam Perjalanan',
            date: '',
            location: '',
            remarks: '',
        }),

        supplier: () => ({}),

        today: '',
        currentMonth: '',

        knowledgeItems: () => [],
    },
)

/*
|--------------------------------------------------------------------------
| Tracking Search
|--------------------------------------------------------------------------
*/

const trackingType = ref('bl_number')
const trackingNumber = ref('')

const trackingOptions = [
    {
        value: 'bl_number',
        label: 'Nomor BL',
    },
    {
        value: 'po_number',
        label: 'Nomor PO',
    },
    {
        value: 'inv_number',
        label: 'Nomor Faktur',
    },
    {
        value: 'product_id',
        label: 'Nomor Produk',
    },
]

const trackingLabel = computed(() => {
    return (
        trackingOptions.find(
            (item) => item.value === trackingType.value,
        )?.label ?? 'Nomor BL'
    )
})

function trackShipment() {
    if (!trackingNumber.value.trim()) {
        return
    }

    router.get('/tracking', {
        type: trackingType.value,
        reference: trackingNumber.value,
    })
}

/*
|--------------------------------------------------------------------------
| Arrival Schedule
|--------------------------------------------------------------------------
*/

const arrivalTab = ref<'week' | 'month'>('week')

const weekDays = [
    {
        day: 'Sen',
        date: '12',
    },
    {
        day: 'Sel',
        date: '13',
    },
    {
        day: 'Rab',
        date: '14',
    },
    {
        day: 'Kam',
        date: '15',
    },
    {
        day: 'Jum',
        date: '16',
    },
    {
        day: 'Sab',
        date: '17',
    },
    {
        day: 'Min',
        date: '18',
    },
]

const arrivalShipments = computed(() => {
    return props.shipments
        .filter((shipment) => shipment.eta || shipment.eta_actual)
        .slice(0, 7)
})

function shipmentTime(shipment: Shipment) {
    const date = shipment.eta_actual ?? shipment.eta

    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return date
    }

    return parsed.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

function shipmentDate(shipment: Shipment) {
    const date = shipment.eta_actual ?? shipment.eta

    if (!date) {
        return null
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return null
    }

    return parsed.getDate()
}

function shipmentCategory(shipment: Shipment) {
    if (shipment.vessel_name) {
        return 'Impor'
    }

    return 'Lokal'
}

function shipmentIcon(shipment: Shipment) {
    if (shipment.vessel_name) {
        return Ship
    }

    return Truck
}

/*
|--------------------------------------------------------------------------
| Ongoing Shipments
|--------------------------------------------------------------------------
*/

const ongoingShipments = computed(() => {
    return props.shipments.filter((shipment) => {
        const status = String(
            shipment.status ?? shipment.tracking_status ?? '',
        ).toLowerCase()

        return ![
            'completed',
            'complete',
            'delivered',
            'selesai',
        ].includes(status)
    })
})

const timelineSteps = [
    'PO Dibuat',
    'Dikonfirmasi',
    'Produksi',
    'Barang Siap',
    'Booking Kapal',
    'ETD',
    'Berangkat',
    'Perjalanan',
    'Tiba di Gudang',
]

function getProgress(shipment: Shipment) {
    const status = String(
        shipment.status ?? shipment.tracking_status ?? '',
    ).toLowerCase()

    if (status.includes('warehouse')) return 100
    if (status.includes('arrival')) return 90
    if (status.includes('perjalanan')) return 75
    if (status.includes('depart')) return 65
    if (status.includes('etd')) return 55
    if (status.includes('booking')) return 45
    if (status.includes('ready')) return 35
    if (status.includes('production')) return 25
    if (status.includes('confirm')) return 15

    return 50
}

function getCurrentStep(shipment: Shipment) {
    return Math.min(
        Math.floor(getProgress(shipment) / 100 * timelineSteps.length),
        timelineSteps.length - 1,
    )
}

function getShipmentStatus(shipment: Shipment) {
    return shipment.status ?? shipment.tracking_status ?? 'Berjalan'
}

function getSupplierName(shipment: Shipment) {
    return (
        shipment.supplier?.supplier_name ??
        props.supplier?.supplier_name ??
        'Supplier'
    )
}

function getSupplierCountry(shipment: Shipment) {
    return (
        shipment.supplier?.country ??
        props.supplier?.country ??
        'Indonesia'
    )
}

function getShipmentReference(shipment: Shipment) {
    return (
        shipment.bl_number ??
        shipment.voyage_number ??
        `Shipment #${shipment.id}`
    )
}

function formatDate(value?: string | null) {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

function openShipment(shipment: Shipment) {
    router.get(`/shipments/${shipment.id}`)
}

/*
|--------------------------------------------------------------------------
| Knowledge Base
|--------------------------------------------------------------------------
*/

const defaultKnowledgeItems: KnowledgeItem[] = [
    {
        title: 'Regulasi Impor',
        category: 'Kepatuhan',
        description:
            'Pembaruan terbaru mengenai perubahan regulasi impor Indonesia untuk periode fiskal.',
        image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuCFjlaweV_5k3HuVcnLxJ5JyQGF872m-BS-ILVQY2sNtEyYxGFSBgnncSpVlJN8pqBt5BYaLYkglTI5E1lG31fQtz3AiimfhpglHYVPOnogVr3mjh0Z2ImLDpRIbVZuPkMEEtk1R4GOnSVoKDl8SnmZx4VUL2jkxx04qQL86SxQYCvNzM2l9cLSG1nNKJjMqQxXAecmmmu3AUDbUAeNFddBQkmHFdJShzvvMW4uTfQk2BStBG0OY4DoYz7SqNoEn0iikUtId7va5e4b',
    },
    {
        title: 'Prosedur Bea Cukai',
        category: 'Prosedur',
        description:
            'Panduan langkah demi langkah untuk mempercepat penyelesaian bea cukai di pelabuhan utama Indonesia.',
        image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuA2rZHvTfvKMWX5vaUKhtJTOnIwxBGpKtpdA9JbCb3ya29jaR-J8QZGNCuC_g2qJc2jQ5IwiACYTRRDuzkjlHQZijE29Q7oj8GIvg-CisedCA7B5nau4_WrTVuy3yTrlVPYzjqXC-2vW3d34d5Ra6wVFc4uv_oUECYjZbOU9dCJx-YSICywA23MelhnjADpr2a-T1h7EtD26SATvMUnO__a_JZYU3UQiaNl3DeN8McmJeYt756nTZ4QQTtf6RflvdRaDAs3LdNRAn3q',
    },
    {
        title: 'Cek LARTAS',
        category: 'LARTAS',
        description:
            'Cari di database terintegrasi untuk daftar barang yang dilarang atau dibatasi.',
        image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDfojcZq811agXfcFyUlg-s5VKOMd-GHoiUzXJGnl5M7eE9hAF7nTqywdMAhBHCVp5jCF7ccj8rNg0zWf0UOX2D_TWsNNw6FFYr0zJX3XwjH2Wc6q2e3bLzzEAp78G0SPx-1e9ymH_C5_QTpC9aiJUWYtUQlJu6SKcH2AENsjGEWLi5bnGKdESJrfR8gL90bd6D_N8-nmwqtOHR0qLS1kw_Rgypty8r0sWFjGAT-Ltd4jJ9hj3tBEUssCgz2bRe3JKb9G1hNOlmjsBP',
    },
]

const knowledgeItems = computed(() => {
    return props.knowledgeItems.length
        ? props.knowledgeItems
        : defaultKnowledgeItems
})
</script>

<template>
    <div class="min-h-screen bg-surface text-on-surface p-4">
        <!-- ========================================================= -->
        <!-- TOP NAVIGATION                                            -->
        <!-- ========================================================= -->

        <header class="sticky top-0 z-50 border-b border-border bg-surface/95 shadow-sm backdrop-blur">
            <nav
                class="mx-auto flex h-20 w-full max-w-container-max items-center justify-between px-4 lg:px-margin-desktop">
                <div class="flex items-center gap-8">
                    <Link href="/" class="font-display text-lg font-bold text-primary md:text-headline-md">
                        PT. CSA | Supply Chain Tower
                    </Link>

                    <div class="hidden items-center gap-6 md:flex">
                        <Link href="/shipments"
                            class="font-medium text-on-surface-variant transition-colors hover:text-primary">
                            Pengiriman
                        </Link>

                        <a href="#armada"
                            class="font-medium text-on-surface-variant transition-colors hover:text-primary">
                            Armada
                        </a>

                        <a href="#analitik"
                            class="font-medium text-on-surface-variant transition-colors hover:text-primary">
                            Analitik
                        </a>

                        <a href="#jaringan"
                            class="font-medium text-on-surface-variant transition-colors hover:text-primary">
                            Jaringan Global
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <Button variant="ghost" size="icon" class="rounded-full">
                        <Bell class="h-5 w-5" />
                    </Button>

                    <Button variant="ghost" size="icon" class="hidden rounded-full sm:flex">
                        <HelpCircle class="h-5 w-5" />
                    </Button>

                    <div class="hidden h-8 w-px bg-border md:block" />

                    <Link href="/contact"
                        class="hidden px-4 py-2 font-medium text-on-surface-variant transition-colors hover:text-primary md:block">
                        Hubungi Admin
                    </Link>

                    <Link href="/login"
                        class="rounded-lg bg-primary px-4 py-2.5 font-semibold text-white transition-all hover:opacity-90 md:px-5">
                        Login
                    </Link>
                </div>
            </nav>
        </header>

        <main>
            <!-- ===================================================== -->
            <!-- HERO                                                  -->
            <!-- ===================================================== -->

            <section
                class="relative overflow-hidden bg-gradient-to-b from-primary/5 via-surface to-surface px-4 pb-24 pt-20 text-center lg:px-margin-desktop lg:pb-32 lg:pt-24">
                <div class="relative z-10 mx-auto max-w-4xl">
                    <h1 class="mb-6 font-display text-4xl font-bold leading-tight text-primary md:text-display">
                        Pantau Rantai Pasok Anda dengan Presisi Real-Time
                    </h1>

                    <p class="mx-auto mb-12 max-w-2xl text-base leading-d md:text-body-lg">
                        Monitor perjalanan logistik ujung-ke-ujung Anda dari
                        titik asal hingga tujuan akhir dengan mesin orkestrasi
                        logistik canggih PT. CSA.
                    </p>

                    <!-- Tracking -->
                    <form @submit.prevent="trackShipment"
                        class="mx-auto flex w-full flex-col gap-3 rounded-xl border border-border bg-surface p-3 shadow-xl md:flex-row">
                        <div class="relative shrink-0">
                            <select v-model="trackingType"
                                class="h-14 w-full appearance-none rounded-lg border-0 bg-surface-container-low pl-4 pr-10 font-medium text-on-surface-variant outline-none focus:ring-2 focus:ring-primary md:w-48">
                                <option v-for="option in trackingOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>

                            <ChevronDown
                                class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-outline" />
                        </div>

                        <div class="relative flex-grow">
                            <Input v-model="trackingNumber"
                                class="h-14 w-full rounded-lg border-none bg-white px-4 text-body-lg focus:ring-2 focus:ring-primary"
                                :placeholder="`Masukkan ${trackingLabel.toLowerCase()} (misal: CSA-99210-BL)`" />
                        </div>

                    </form>

                    <!-- Statistics -->
                    <div class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-3 text-label-md">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-success" />
                            {{ activeShipments.toLocaleString('id-ID') }}
                            Pengiriman Aktif
                        </span>

                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-info" />
                            {{ vesselsAtSea }}
                            Kapal di Laut
                        </span>

                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-warning" />
                            {{ portResolutionsToday }}
                            Penyelesaian Pelabuhan Hari Ini
                        </span>
                    </div>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- ARRIVAL SCHEDULE                                      -->
            <!-- ===================================================== -->

            <section class="mx-auto max-w-container-max px-4 py-16 lg:px-margin-desktop lg:py-xl">
                <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <h2 class="mb-2 font-display text-2xl font-bold text-primary md:text-headline-lg">
                            Jadwal Kedatangan Shipment
                        </h2>

                        <p class="text-body-md">
                            Pemantauan kedatangan mingguan terintegrasi untuk
                            semua saluran logistik.
                        </p>
                    </div>

                    <div class="flex w-fit rounded-lg border border-border bg-surface-container-low p-1">
                        <button type="button" @click="arrivalTab = 'week'"
                            class="rounded-md px-6 py-2 text-label-md transition-colors" :class="arrivalTab === 'week'
                                ? 'bg-white font-semibold text-primary shadow-sm'
                                : 'font-mediud hover:text-primary'
                                ">
                            Mingguan
                        </button>

                        <button type="button" @click="arrivalTab = 'month'"
                            class="rounded-md px-6 py-2 text-label-md transition-colors" :class="arrivalTab === 'month'
                                ? 'bg-white font-semibold text-primary shadow-sm'
                                : 'font-mediud hover:text-primary'
                                ">
                            Bulanan
                        </button>
                    </div>
                </div>

                <!-- Weekly -->
                <div v-if="arrivalTab === 'week'" class="grid grid-cols-1 gap-4 md:grid-cols-7">
                    <div v-for="day in weekDays" :key="`${day.day}-${day.date}`" class="flex min-w-0 flex-col gap-4">
                        <div class="mb-2 border-b border-border py-2 text-center">
                            <span class="block text-label-sm uppercase tracking-wider">
                                {{ day.day }}
                            </span>

                            <span class="font-display text-headline-md text-primary">
                                {{ day.date }}
                            </span>
                        </div>

                        <template v-for="shipment in arrivalShipments.filter(
                            (item) =>
                                shipmentDate(item) === Number(day.date),
                        )" :key="shipment.id">
                            <div class="rounded-lg border p-4" :class="shipment.vessel_name
                                ? 'border-primary/10 bg-primary text-white shadow-md'
                                : 'border-secondary-container/20 bg-secondary-fixed text-on-secondary-container'
                                ">
                                <div class="mb-2 flex items-start justify-between">
                                    <span class="text-label-sm font-bold uppercase opacity-80">
                                        {{ shipmentCategory(shipment) }}
                                    </span>

                                    <component :is="shipmentIcon(shipment)" class="h-5 w-5" />
                                </div>

                                <p class="mb-1 font-bold leading-tight text-body-md">
                                    {{
                                        shipment.vessel_name ??
                                        shipment.voyage_number ??
                                        getShipmentReference(shipment)
                                    }}
                                </p>

                                <p class="mb-3 text-[11px] opacity-70">
                                    {{
                                        shipment.port_of_discharge_id ??
                                        'Pelabuhan Tujuan'
                                    }}
                                </p>

                                <div
                                    class="flex w-fit items-center gap-1 rounded-full bg-white/10 px-2 py-1 text-[10px]">
                                    <Clock3 class="h-3 w-3" />
                                    {{ shipmentTime(shipment) }}
                                </div>
                            </div>
                        </template>

                        <div v-if="
                            !arrivalShipments.some(
                                (item) =>
                                    shipmentDate(item) === Number(day.date),
                            )
                        "
                            class="flex h-24 items-center justify-center rounded-lg border border-dashed border-outline-variant bg-surface-container-low px-2 text-center">
                            <span class="text-label-sm text-outline">
                                Tidak Ada Kedatangan
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Monthly -->
                <div v-else class="rounded-xl border border-border bg-surface p-8 text-center shadow-sm">
                    <p class="text-body-md">
                        Menampilkan jadwal kedatangan untuk
                        <strong class="text-primary">
                            {{ currentMonth || 'bulan berjalan' }}
                        </strong>
                        .
                    </p>

                    <Link href="/shipments" class="mt-4 inline-flex items-center gap-2 font-semibold text-primary">
                        Lihat seluruh shipment
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- ONGOING SHIPMENTS                                     -->
            <!-- ===================================================== -->

            <section class="bg-surface-container-low px-4 py-16 lg:px-margin-desktop lg:py-xl">
                <div class="mx-auto max-w-container-max">
                    <div class="mb-8">
                        <h2 class="mb-2 font-display text-2xl font-bold text-primary md:text-headline-lg">
                            Timeline Pengiriman Berjalan
                        </h2>

                        <p class="text-body-md">
                            Status real-time dari pengiriman kritis di seluruh
                            jaringan rantai pasok.
                        </p>
                    </div>

                    <div v-if="ongoingShipments.length" class="space-y-6">
                        <div v-for="shipment in ongoingShipments" :key="shipment.id"
                            class="overflow-hidden rounded-xl border border-border bg-surface shadow-sm">
                            <!-- Shipment Header -->
                            <div class="border-b border-border bg-surface-container-lowest/50 p-6">
                                <div class="flex flex-wrap items-center justify-between gap-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-primary/10 bg-primary-fixed">
                                            <Package class="h-6 w-6 font-bold text-primary" />
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="font-display text-lg font-bold text-primary">
                                                    {{
                                                        getShipmentReference(
                                                            shipment,
                                                        )
                                                    }}
                                                </h4>

                                                <span
                                                    class="rounded border border-success/20 bg-success/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-tighter text-success">
                                                    {{
                                                        getShipmentStatus(
                                                            shipment,
                                                        )
                                                    }}
                                                </span>
                                            </div>

                                            <p class="text-body-md">
                                                {{
                                                    getSupplierName(shipment)
                                                }}

                                                <span class="mx-2">•</span>

                                                {{
                                                    getSupplierCountry(
                                                        shipment,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-6 md:gap-8">
                                        <div class="text-center">
                                            <p class="mb-1 text-[10px] font-bold uppercase tracking-widesd">
                                                ETD
                                            </p>

                                            <p class="font-label-md text-on-surface">
                                                {{
                                                    formatDate(
                                                        shipment.etd_actual ??
                                                        shipment.etd,
                                                    )
                                                }}
                                            </p>
                                        </div>

                                        <div class="text-center">
                                            <p class="mb-1 text-[10px] font-bold uppercase tracking-widesd">
                                                ETA
                                            </p>

                                            <p class="font-label-md text-on-surface">
                                                {{
                                                    formatDate(
                                                        shipment.eta_actual ??
                                                        shipment.eta,
                                                    )
                                                }}
                                            </p>
                                        </div>

                                        <div class="flex flex-col items-end">
                                            <div class="mb-1 flex items-center gap-2">
                                                <p class="text-[10px] font-bold">
                                                    PROGRES
                                                </p>

                                                <p class="text-label-md font-bold text-primary">
                                                    {{
                                                        getProgress(
                                                            shipment,
                                                        )
                                                    }}%
                                                </p>
                                            </div>

                                            <div
                                                class="h-1.5 w-32 overflow-hidden rounded-full bg-surface-container-high">
                                                <div class="h-full rounded-full bg-primary" :style="{
                                                    width: `${getProgress(shipment)}%`,
                                                }" />
                                            </div>
                                        </div>

                                        <Button variant="ghost" size="icon" class="rounded-full">
                                            <MoreVertical class="h-5 w-5" />
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="custom-scrollbar overflow-x-auto p-6">
                                <div class="relative flex min-w-[900px] justify-between">
                                    <!-- Connecting line -->
                                    <div class="absolute left-[5%] right-[5%] top-3 h-0.5 bg-border" />

                                    <div v-for="(
step, index
                                        ) in timelineSteps" :key="step"
                                        class="relative z-10 flex w-24 flex-col items-center">
                                        <div class="mb-3 flex h-7 w-7 items-center justify-center rounded-full border-4 border-white shadow-sm"
                                            :class="index <
                                                getCurrentStep(shipment)
                                                ? 'bg-success'
                                                : index ===
                                                    getCurrentStep(
                                                        shipment,
                                                    )
                                                    ? 'animate-pulse bg-primary shadow-lg shadow-primary/20'
                                                    : 'bg-surface-container-high border-2 border-border'
                                                ">
                                            <Check v-if="
                                                index <
                                                getCurrentStep(shipment)
                                            " class="h-3 w-3 text-white" />

                                            <Ship v-else-if="
                                                index ===
                                                getCurrentStep(shipment)
                                            " class="h-3 w-3 text-white" />
                                        </div>

                                        <p class="text-center text-[9px] font-bold uppercase" :class="index <
                                            getCurrentStep(shipment)
                                            ? 'text-success'
                                            : index ===
                                                getCurrentStep(
                                                    shipment,
                                                )
                                                ? 'text-primary'
                                                : 'text-surface'
                                            ">
                                            {{ step }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div
                                class="flex flex-col gap-4 border-t border-border bg-surface-container-low/30 px-6 py-4 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-3 text-[11px]">
                                    <span class="font-bold uppercase tracking-widesd">
                                        UPDATE TERAKHIR:
                                    </span>

                                    <span class="flex items-center gap-1 font-medium text-on-surface-variant">
                                        <Clock3 class="h-3.5 w-3.5" />

                                        {{
                                            formatDate(
                                                shipment.eta_actual ??
                                                shipment.shipment_date,
                                            )
                                        }}

                                        <span v-if="
                                            shipment.port_of_discharge_id
                                        ">
                                            • Posisi:
                                            {{
                                                shipment.port_of_discharge_id
                                            }}
                                        </span>
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    <Button variant="ghost" class="gap-2 text-primary">
                                        <FileText class="h-4 w-4" />
                                        Unduh B/L
                                    </Button>

                                    <Button class="gap-2" @click="openShipment(shipment)">
                                        Lihat Detail
                                        <ArrowRight class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="rounded-xl border border-dashed border-border bg-surface p-12 text-center">
                        <Package class="mx-auto mb-4 h-10 w-1d" />

                        <h3 class="mb-2 font-semibold text-primary">
                            Belum ada shipment berjalan
                        </h3>

                        <p class="text-body-md">
                            Data shipment aktif akan muncul di area ini.
                        </p>
                    </div>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- KNOWLEDGE BASE                                        -->
            <!-- ===================================================== -->

            <section class="mx-auto max-w-container-max px-4 py-16 lg:px-margin-desktop lg:py-xl">
                <div class="mb-12">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                        <div>
                            <h2 class="mb-2 font-display text-2xl font-bold text-primary md:text-headline-lg">
                                Pusat Pengetahuan PT. CSA
                            </h2>

                            <p class="text-body-md">
                                Pembaruan kritis dan sumber daya kepatuhan bagi
                                profesional logistik.
                            </p>
                        </div>

                        <Link href="/knowledge"
                            class="mb-1 flex items-center gap-2 font-bold text-primary transition-all hover:gap-3">
                            Lihat Semua
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <article v-for="item in knowledgeItems" :key="item.title" class="group cursor-pointer">
                        <div class="relative mb-4 h-64 overflow-hidden rounded-xl shadow-lg">
                            <img :src="item.image" :alt="item.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />

                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent" />

                            <div class="absolute bottom-6 left-6">
                                <span
                                    class="rounded-full border border-white/30 bg-white/20 px-3 py-1 text-label-sm text-white backdrop-blur-md">
                                    {{ item.category }}
                                </span>
                            </div>
                        </div>

                        <h3
                            class="mb-3 font-display text-xl font-bold text-primary transition-colors group-hover:text-secondary">
                            {{ item.title }}
                        </h3>

                        <p class="mb-4 text-body-md">
                            {{ item.description }}
                        </p>

                        <Link href="/login"
                            class="flex items-center gap-2 font-bold text-primary transition-all hover:gap-3">
                            Login untuk Analisis Lengkap
                            <Lock class="h-4 w-4" />
                        </Link>
                    </article>
                </div>
            </section>
        </main>

        <!-- ========================================================= -->
        <!-- FOOTER                                                    -->
        <!-- ========================================================= -->

        <footer class="mt-12 border-t border-border bg-surface-container-lowest">
            <div
                class="mx-auto flex w-full max-w-container-max flex-col items-center justify-between gap-8 px-4 py-16 md:flex-row lg:px-margin-desktop">
                <div class="text-center md:text-left">
                    <span class="mb-2 block font-display text-xl font-bold text-primary">
                        PT. CSA Supply Chain Tower
                    </span>

                    <p class="max-w-sm text-label-md">
                        Penyedia solusi logistik global terkemuka untuk
                        manajemen dan orkestrasi rantai pasok perusahaan.
                    </p>
                </div>

                <div class="flex flex-col items-center gap-6 md:items-end">
                    <div class="flex flex-wrap justify-center gap-6">
                        <Link href="/privacy" class="text-body-md transition-colors hover:text-secondary">
                            Kebijakan Privasi
                        </Link>

                        <Link href="/terms" class="text-body-md transition-colors hover:text-secondary">
                            Syarat & Ketentuan
                        </Link>

                        <Link href="/compliance" class="text-body-md transition-colors hover:text-secondary">
                            Kepatuhan
                        </Link>
                    </div>

                    <div class="flex gap-4">
                        <Button variant="outline" size="icon" class="rounded-full">
                            <Globe class="h-5 w-5 text-primary" />
                        </Button>

                        <Button variant="outline" size="icon" class="rounded-full">
                            <Mail class="h-5 w-5 text-primary" />
                        </Button>
                    </div>
                </div>
            </div>

            <div class="border-t border-border/50 py-6 text-center">
                <p class="text-label-sd">
                    © 2026 PT. CSA Supply Chain Tower. Hak cipta dilindungi
                    undang-undang.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* =========================================================
   BASE
========================================================= */

:global(html) {
    scroll-behavior: smooth;
}

:global(body) {
    margin: 0;
    background: #f8fafc;
    color: #0f172a;
    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}

* {
    box-sizing: border-box;
}

/* =========================================================
   SCROLLBAR
========================================================= */

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(100, 116, 139, 0.35) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(100, 116, 139, 0.28);
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 116, 139, 0.45);
}

/* =========================================================
   HERO
========================================================= */

.hero-section {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at 50% 0%,
            rgba(30, 64, 175, 0.10),
            transparent 42%),
        linear-gradient(180deg,
            #eff6ff 0%,
            #f8fafc 65%,
            #f8fafc 100%);
}

.hero-section::before {
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    top: -220px;
    left: -180px;
    border-radius: 9999px;
    background: rgba(59, 130, 246, 0.08);
    filter: blur(2px);
}

.hero-section::after {
    content: "";
    position: absolute;
    width: 360px;
    height: 360px;
    top: -180px;
    right: -150px;
    border-radius: 9999px;
    background: rgba(14, 165, 233, 0.07);
}

.hero-content {
    position: relative;
    z-index: 2;
}

/* =========================================================
   HERO TITLE
========================================================= */

.hero-title {
    max-width: 850px;
    margin-inline: auto;
    font-size: clamp(2.25rem, 5vw, 4rem);
    line-height: 1.08;
    letter-spacing: -0.045em;
    font-weight: 800;
    color: #0f2a5f;
}

.hero-description {
    max-width: 690px;
    margin-inline: auto;
    color: #64748b;
    font-size: 1.05rem;
    line-height: 1.8;
}

/* =========================================================
   TRACKING BOX
========================================================= */

.tracking-box {
    position: relative;
    z-index: 10;
    display: flex;
    width: min(100%, 820px);
    margin-inline: auto;
    padding: 7px;
    border: 1px solid rgba(148, 163, 184, 0.28);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.94);
    box-shadow:
        0 20px 45px rgba(15, 23, 42, 0.08),
        0 4px 12px rgba(15, 23, 42, 0.04);
    backdrop-filter: blur(12px);
}

.tracking-box:focus-within {
    border-color: rgba(37, 99, 235, 0.35);
    box-shadow:
        0 20px 50px rgba(37, 99, 235, 0.10),
        0 0 0 4px rgba(37, 99, 235, 0.06);
}

.tracking-select {
    min-width: 155px;
    height: 52px;
    border: 0;
    border-radius: 11px;
    background: #f1f5f9;
    color: #334155;
    padding: 0 38px 0 16px;
    font-size: 0.875rem;
    font-weight: 600;
    outline: none;
}

.tracking-input {
    height: 52px;
    border: 0;
    outline: none;
    background: transparent;
    color: #0f172a;
    font-size: 0.95rem;
}

.tracking-input::placeholder {
    color: #94a3b8;
}

.tracking-button {
    min-width: 170px;
    height: 52px;
    border-radius: 11px;
    font-weight: 700;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
}

/* =========================================================
   STATISTICS
========================================================= */

.hero-stat {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 600;
}

.hero-stat-dot {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #22c55e;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.10);
}

/* =========================================================
   SECTION
========================================================= */

.section-container {
    width: min(100% - 32px, 1280px);
    margin-inline: auto;
}

.section-heading {
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    line-height: 1.2;
    letter-spacing: -0.025em;
    font-weight: 750;
    color: #0f2a5f;
}

.section-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.65;
}

/* =========================================================
   TAB
========================================================= */

.arrival-tabs {
    display: inline-flex;
    padding: 4px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f1f5f9;
}

.arrival-tab {
    min-width: 105px;
    padding: 8px 16px;
    border-radius: 7px;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 600;
    transition:
        background 0.2s ease,
        color 0.2s ease,
        box-shadow 0.2s ease;
}

.arrival-tab:hover {
    color: #1e3a8a;
}

.arrival-tab.active {
    background: #ffffff;
    color: #1d4ed8;
    box-shadow: 0 2px 5px rgba(15, 23, 42, 0.08);
}

/* =========================================================
   ARRIVAL DAY
========================================================= */

.arrival-day {
    min-height: 250px;
}

.arrival-day-header {
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
}

.arrival-day-name {
    display: block;
    margin-bottom: 3px;
    color: #94a3b8;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.arrival-day-number {
    color: #0f2a5f;
    font-size: 1.5rem;
    font-weight: 800;
}

/* =========================================================
   ARRIVAL CARD
========================================================= */

.arrival-card {
    position: relative;
    overflow: hidden;
    min-height: 135px;
    padding: 16px;
    border-radius: 12px;
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.arrival-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.10);
}

.arrival-card.primary {
    color: white;
    background:
        linear-gradient(145deg,
            #1e40af 0%,
            #1d4ed8 60%,
            #2563eb 100%);
    box-shadow:
        0 10px 25px rgba(30, 64, 175, 0.18);
}

.arrival-card.secondary {
    border: 1px solid #dbeafe;
    color: #1e3a8a;
    background: #eff6ff;
}

.arrival-card-label {
    margin-bottom: 8px;
    font-size: 0.63rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.75;
}

.arrival-card-title {
    margin-bottom: 5px;
    font-size: 0.92rem;
    line-height: 1.3;
    font-weight: 750;
}

.arrival-card-location {
    margin-bottom: 12px;
    color: inherit;
    font-size: 0.68rem;
    opacity: 0.7;
}

.arrival-card-time {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    font-size: 0.65rem;
    font-weight: 700;
}

.arrival-empty {
    display: flex;
    min-height: 135px;
    align-items: center;
    justify-content: center;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    color: #94a3b8;
    text-align: center;
    font-size: 0.68rem;
    font-weight: 600;
}

/* =========================================================
   SHIPMENT SECTION
========================================================= */

.shipment-section {
    background:
        linear-gradient(180deg,
            #f1f5f9 0%,
            #f8fafc 100%);
}

.shipment-card {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
    box-shadow:
        0 4px 15px rgba(15, 23, 42, 0.04);
    transition:
        box-shadow 0.25s ease,
        transform 0.25s ease;
}

.shipment-card:hover {
    transform: translateY(-2px);
    box-shadow:
        0 14px 30px rgba(15, 23, 42, 0.08);
}

/* =========================================================
   SHIPMENT HEADER
========================================================= */

.shipment-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eef2f7;
}

.shipment-icon {
    display: flex;
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border: 1px solid #dbeafe;
    border-radius: 11px;
    background: #eff6ff;
    color: #1d4ed8;
}

.shipment-reference {
    color: #0f2a5f;
    font-size: 1rem;
    font-weight: 750;
}

.shipment-supplier {
    margin-top: 4px;
    color: #64748b;
    font-size: 0.75rem;
}

.shipment-status {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #15803d;
    font-size: 0.6rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* =========================================================
   ETA / ETD
========================================================= */

.shipment-meta {
    text-align: center;
}

.shipment-meta-label {
    margin-bottom: 3px;
    color: #94a3b8;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.shipment-meta-value {
    color: #334155;
    font-size: 0.78rem;
    font-weight: 700;
}

/* =========================================================
   PROGRESS
========================================================= */

.shipment-progress {
    min-width: 145px;
}

.shipment-progress-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
    color: #94a3b8;
    font-size: 0.58rem;
    font-weight: 800;
}

.shipment-progress-value {
    color: #1d4ed8;
}

.shipment-progress-track {
    height: 5px;
    overflow: hidden;
    border-radius: 999px;
    background: #e2e8f0;
}

.shipment-progress-bar {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg,
            #2563eb,
            #3b82f6);
}

/* =========================================================
   TIMELINE
========================================================= */

.timeline-wrapper {
    padding: 28px 24px;
    background: #ffffff;
}

.timeline {
    position: relative;
    display: flex;
    min-width: 900px;
    justify-content: space-between;
}

.timeline-line {
    position: absolute;
    top: 12px;
    left: 5%;
    right: 5%;
    height: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    z-index: 2;
    display: flex;
    width: 95px;
    flex-direction: column;
    align-items: center;
}

.timeline-dot {
    display: flex;
    width: 25px;
    height: 25px;
    align-items: center;
    justify-content: center;
    margin-bottom: 9px;
    border: 4px solid #ffffff;
    border-radius: 999px;
    background: #cbd5e1;
    box-shadow: 0 0 0 1px #e2e8f0;
}

.timeline-dot.completed {
    background: #22c55e;
    box-shadow:
        0 0 0 1px #22c55e,
        0 3px 8px rgba(34, 197, 94, 0.22);
}

.timeline-dot.current {
    background: #2563eb;
    box-shadow:
        0 0 0 4px rgba(37, 99, 235, 0.10),
        0 4px 10px rgba(37, 99, 235, 0.25);
}

.timeline-label {
    max-width: 90px;
    color: #94a3b8;
    text-align: center;
    font-size: 0.58rem;
    line-height: 1.3;
    font-weight: 700;
    text-transform: uppercase;
}

.timeline-label.completed {
    color: #16a34a;
}

.timeline-label.current {
    color: #1d4ed8;
}

/* =========================================================
   SHIPMENT FOOTER
========================================================= */

.shipment-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 24px;
    border-top: 1px solid #eef2f7;
    background: #f8fafc;
}

.shipment-update {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #64748b;
    font-size: 0.68rem;
}

.shipment-update strong {
    color: #94a3b8;
    font-size: 0.58rem;
    letter-spacing: 0.06em;
}

/* =========================================================
   KNOWLEDGE CARD
========================================================= */

.knowledge-card {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.knowledge-card:hover {
    transform: translateY(-5px);
    box-shadow:
        0 18px 35px rgba(15, 23, 42, 0.09);
}

.knowledge-image {
    position: relative;
    height: 210px;
    overflow: hidden;
}

.knowledge-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.knowledge-card:hover .knowledge-image img {
    transform: scale(1.05);
}

.knowledge-overlay {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg,
            transparent 35%,
            rgba(15, 42, 95, 0.72) 100%);
}

.knowledge-category {
    position: absolute;
    bottom: 16px;
    left: 16px;
    padding: 5px 10px;
    border: 1px solid rgba(255, 255, 255, 0.30);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.16);
    color: white;
    font-size: 0.62rem;
    font-weight: 700;
    backdrop-filter: blur(8px);
}

.knowledge-body {
    padding: 20px;
}

.knowledge-title {
    margin-bottom: 8px;
    color: #0f2a5f;
    font-size: 1.05rem;
    line-height: 1.35;
    font-weight: 750;
}

.knowledge-description {
    min-height: 50px;
    color: #64748b;
    font-size: 0.78rem;
    line-height: 1.7;
}

/* =========================================================
   FOOTER
========================================================= */

.site-footer {
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
}

.footer-title {
    color: #0f2a5f;
    font-size: 1.1rem;
    font-weight: 800;
}

.footer-description {
    max-width: 380px;
    color: #64748b;
    font-size: 0.75rem;
    line-height: 1.7;
}

.footer-link {
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.footer-link:hover {
    color: #1d4ed8;
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.2rem;
        letter-spacing: -0.035em;
    }

    .hero-description {
        font-size: 0.9rem;
        line-height: 1.7;
    }

    .tracking-box {
        flex-direction: column;
        padding: 8px;
    }

    .tracking-select,
    .tracking-button {
        width: 100%;
    }

    .tracking-input {
        width: 100%;
        padding-inline: 12px;
    }

    .shipment-header {
        padding: 18px;
    }

    .shipment-footer {
        align-items: flex-start;
        flex-direction: column;
        padding: 14px 18px;
    }

    .shipment-footer>div:last-child {
        width: 100%;
    }

    .shipment-footer button {
        flex: 1;
    }

    .timeline-wrapper {
        padding: 22px 18px;
    }

    .knowledge-image {
        height: 190px;
    }
}

/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.9rem;
    }

    .section-container {
        width: min(100% - 24px, 1280px);
    }

    .hero-stat {
        width: 100%;
        justify-content: center;
    }

    .arrival-tabs {
        width: 100%;
    }

    .arrival-tab {
        flex: 1;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
    }
}
</style>