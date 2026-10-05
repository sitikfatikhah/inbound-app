<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import Kpi from '@/components/kpi.vue';
import InteractiveArea from '@/components/InteractiveArea.vue';
import TableSection from '@/components/TableSection.vue';

interface DashboardProps {
    stats: Record<string, number>;
    timeline: Array<{ label: string; date: string; target: number; actual: number }>;
    statusBreakdown: Array<{ key: string; label: string; color: string; count: number }>;
    exceptions: Array<{
        id: number;
        reference: string;
        detail: string;
        product: string;
        vendor: string;
        status: string;
        eta: string | null;
        action: string;
    }>;
}

defineProps<DashboardProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />
    <div class="min-h-full flex-1 overflow-x-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
        <div class="mx-auto flex max-w-[1680px] flex-col gap-8">
            <Kpi :stats="stats" />
            <InteractiveArea :timeline="timeline" :status-breakdown="statusBreakdown" />
            <TableSection :exceptions="exceptions" />
        </div>
    </div>
</template>
