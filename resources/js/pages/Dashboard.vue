<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { dashboard } from '@/routes'

import Kpi from '@/components/kpi.vue'
import InteractiveArea from '@/components/InteractiveArea.vue'
import TableSection from '@/components/TableSection.vue'

interface DashboardStats {
    total_shipments: number
    in_transit: number
    customs_clearance: number
    arriving_today: number
}

interface TimelineItem {
    label: string
    date: string
    target: number
    actual: number
}

interface StatusBreakdownItem {
    key: string
    label: string
    color: 'primary' | 'success' | 'warning' | 'error'
    count: number
}

interface ShipmentException {
    id: number
    reference: string
    detail: string
    product: string
    vendor: string
    status: string
    eta: string | null
    action: string
}

interface DashboardProps {
    stats: DashboardStats
    timeline: TimelineItem[]
    statusBreakdown: StatusBreakdownItem[]
    exceptions: ShipmentException[]
}

defineProps<DashboardProps>()

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
})
</script>

<template>
    <Head title="Control Tower Dashboard" />

    <main
        class="min-h-full flex-1 overflow-x-hidden bg-slate-50 p-4 sm:p-6 lg:p-8"
    >
        <div class="mx-auto flex w-full max-w-[1680px] flex-col gap-6 sm:gap-8">
            <!-- Dashboard heading -->
            <section
                class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                        Supply Chain Control Tower
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Monitoring inbound import dan PO lokal PT. CSA.
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600"
                >
                    <span class="relative flex size-2">
                        <span
                            class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-50"
                        />
                        <span
                            class="relative inline-flex size-2 rounded-full bg-emerald-500"
                        />
                    </span>

                    Dynamics 365
                </div>
            </section>

            <!-- KPI summary -->
            <section aria-label="Shipment summary">
                <Kpi :stats="stats as any" />
            </section>

            <!-- Shipment timeline and status distribution -->
            <section aria-label="Shipment performance and status">
                <InteractiveArea
                    :timeline="timeline"
                    :status-breakdown="statusBreakdown"
                />
            </section>

            <!-- Shipment requiring attention -->
            <section aria-label="Shipments requiring attention">
                <TableSection :exceptions="exceptions" />
            </section>

            <footer
                class="border-t border-slate-200 pt-4 text-xs text-slate-400"
            >
                PT. CSA · Inbound Shipment Monitoring
            </footer>
        </div>
    </main>
</template>