<script setup lang="ts">
import { computed } from 'vue';
import {
    Activity,
    AlertTriangle,
    CheckCircle2,
    CircleDot,
    Ship,
} from 'lucide-vue-next';

interface TimelinePoint {
    label: string;
    date: string;
    target: number;
    actual: number;
}

interface StatusPoint {
    key: string;
    label: string;
    color: 'primary' | 'success' | 'warning' | 'error';
    count: number;
}
const props = defineProps<{
    timeline: TimelinePoint[];
    statusBreakdown: StatusPoint[];
}>();

const maxTimeline = computed(() =>
    Math.max(
        ...props.timeline.map((point) =>
            Math.max(point.target, point.actual),
        ),
        1,
    ),
);

const totalTracked = computed(() =>
    props.statusBreakdown.reduce(
        (total, item) => total + item.count,
        0,
    ),
);

const delayedCount = computed(
    () =>
        props.statusBreakdown.find(
            (item) => item.key === 'delayed',
        )?.count ?? 0,
);

const insight = computed(() => {
    if (delayedCount.value > 0) {
        return `${delayedCount.value} shipment perlu ditindaklanjuti sebelum ETA terlewat.`;
    }

    return 'Tidak ada shipment yang melewati ETA.';
});
</script>

<template>
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- TIMELINE -->
        <article
            class="flex min-h-[420px] flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm xl:col-span-2">
            <header
                class="flex flex-col gap-4 border-b border-border p-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-primary/10 p-2.5 text-primary">
                        <Activity class="size-5" />
                    </div>

                    <div>
                        <h2 class="text-lg font-bold">
                            Insight Timeline Shipment
                        </h2>

                        <p class="text-xs text-muted-foreground">
                            Target ETA dan realisasi ATA
                        </p>
                    </div>
                </div>

                <span class="rounded-lg bg-muted px-3 py-2 text-xs font-semibold text-muted-foreground">
                    7 hari
                </span>
            </header>

            <div class="flex flex-1 flex-col justify-end gap-6 p-6">
                <div v-if="timeline.length" class="grid h-64 grid-cols-7 items-end gap-2 sm:gap-4">
                    <div v-for="point in timeline" :key="point.date"
                        class="flex h-full flex-col items-center justify-end gap-2">
                        <div class="flex h-full w-full items-end justify-center gap-1 sm:gap-2">
                            <!-- TARGET = ETA -->
                            <div class="w-1/2 rounded-t-md bg-primary/25 transition-all" :style="{
                                height: `${Math.max(
                                    (point.target / maxTimeline) * 100,
                                    point.target ? 8 : 2,
                                )}%`,
                            }" :title="`ETA: ${point.target}`" />

                            <!-- ACTUAL = ATA -->
                            <div class="w-1/2 rounded-t-md bg-primary transition-all" :style="{
                                height: `${Math.max(
                                    (point.actual / maxTimeline) * 100,
                                    point.actual ? 8 : 2,
                                )}%`,
                            }" :title="`ATA: ${point.actual}`" />
                        </div>

                        <span class="text-[11px] text-muted-foreground">
                            {{ point.label }}
                        </span>
                    </div>
                </div>

                <div v-else class="flex h-64 items-center justify-center text-sm text-muted-foreground">
                    Belum ada data shipment untuk timeline.
                </div>

                <div class="flex flex-wrap gap-5 text-xs text-muted-foreground">
                    <span class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-primary/25" />
                        Target kedatangan (ETA)
                    </span>

                    <span class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-primary" />
                        Realisasi kedatangan (ATA)
                    </span>
                </div>
            </div>
        </article>

        <!-- STATUS -->
        <article class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
            <header class="border-b border-border p-6">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-600">
                        <Ship class="size-5" />
                    </div>

                    <div>
                        <h2 class="text-lg font-bold">
                            Monitoring Operasional
                        </h2>

                        <p class="text-xs text-muted-foreground">
                            Distribusi status shipment
                        </p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 p-6">
                <div class="rounded-xl bg-primary p-5 text-primary-foreground">
                    <p class="text-xs opacity-80">
                        Total shipment terpantau
                    </p>

                    <p class="mt-1 text-3xl font-bold">
                        {{ totalTracked }}
                    </p>

                    <p class="mt-2 flex items-center gap-2 text-xs">
                        <CircleDot class="size-3" />
                        {{ insight }}
                    </p>
                </div>

                <div v-for="item in statusBreakdown" :key="item.key" class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="size-2.5 rounded-full" :class="item.color === 'error'
                                ? 'bg-destructive'
                                : item.color === 'success'
                                    ? 'bg-emerald-500'
                                    : item.color === 'warning'
                                        ? 'bg-amber-500'
                                        : 'bg-primary'
                            " />

                        <span class="text-sm text-muted-foreground">
                            {{ item.label }}
                        </span>
                    </div>

                    <span class="text-sm font-bold">
                        {{ item.count }}
                    </span>
                </div>

                <div class="border-t border-border pt-5 text-xs text-muted-foreground">
                    <p class="mb-3 font-semibold text-foreground">
                        Sinyal yang perlu dipantau
                    </p>

                    <p class="flex items-center gap-2">
                        <AlertTriangle class="size-4 text-amber-500" />
                        Prioritaskan shipment yang melewati ETA.
                    </p>

                    <p class="mt-2 flex items-center gap-2">
                        <CheckCircle2 class="size-4 text-emerald-500" />
                        Data diringkas langsung dari tabel shipment.
                    </p>
                </div>
            </div>
        </article>
    </section>
</template>