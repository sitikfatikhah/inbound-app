<script setup lang="ts">
import { computed } from 'vue';
import {
    AlertTriangle,
    CalendarDays,
    ClipboardCheck,
    Clock3,
    PackageCheck,
    Ship,
} from 'lucide-vue-next';

interface DashboardStats {
    totalShipments: number;
    activeShipments: number;
    arrivalsToday: number;
    arrivalsThisWeek: number;
    delayed: number;
    pendingClearance: number;
    notReceived: number;
}

const props = defineProps<{
    stats: DashboardStats;
}>();

const cards = computed(() => [
    {
        label: 'Total Shipment',
        value: props.stats.totalShipments,
        icon: Ship,
        tone: 'primary',
    },
    {
        label: 'Shipment Berjalan',
        value: props.stats.activeShipments,
        icon: Ship,
        tone: 'secondary',
    },
    {
        label: 'Tiba Hari Ini',
        value: props.stats.arrivalsToday,
        icon: CalendarDays,
        tone: 'info',
    },
    {
        label: 'Tiba Minggu Ini',
        value: props.stats.arrivalsThisWeek,
        icon: Clock3,
        tone: 'info',
    },
    {
        label: 'Terlambat',
        value: props.stats.delayed,
        icon: AlertTriangle,
        tone: 'danger',
    },
    {
        label: 'Menunggu Clearance',
        value: props.stats.pendingClearance,
        icon: ClipboardCheck,
        tone: 'warning',
    },
    {
        label: 'Belum Diterima',
        value: props.stats.notReceived,
        icon: PackageCheck,
        tone: 'warning',
    },
]);
</script>

<template>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        <article v-for="card in cards" :key="card.label"
            class="rounded-xl border border-border bg-card p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
            :class="card.tone === 'danger'
                ? 'border-destructive/30 bg-destructive/5'
                : ''
                ">
            <div class="mb-5 flex items-start justify-between">
                <div class="rounded-lg p-2.5" :class="card.tone === 'danger'
                    ? 'bg-destructive/10 text-destructive'
                    : card.tone === 'warning'
                        ? 'bg-amber-500/10 text-amber-600'
                        : card.tone === 'secondary'
                            ? 'bg-sky-500/10 text-sky-600'
                            : 'bg-primary/10 text-primary'
                    ">
                    <component :is="card.icon" class="size-5" />
                </div>

                <span v-if="card.tone === 'danger' && card.value > 0"
                    class="rounded-full bg-destructive/10 px-2 py-1 text-[10px] font-bold uppercase text-destructive">
                    Urgent
                </span>
            </div>

            <p class="text-xs font-medium text-muted-foreground">
                {{ card.label }}
            </p>

            <p class="mt-1 text-2xl font-bold tracking-tight" :class="card.tone === 'danger'
                ? 'text-destructive'
                : 'text-foreground'
                ">
                {{ card.value }}
            </p>
        </article>
    </section>
</template>