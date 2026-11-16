<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface StatusHistory {
    id: number | string;
    status: string | null;
    status_date: string | null;
    status_type?: 'document' | 'shipment' | null;
    location?: string | null;
    country?: string | null;
    remarks?: string | null;
}

const props = withDefaults(
    defineProps<{
        histories: StatusHistory[];
        description?: string;
        emptyMessage?: string;
    }>(),
    {
        description: 'Chronological record of shipment status changes.',
        emptyMessage: 'No status history recorded.',
    },
);

const chronologicalHistories = computed(() =>
    [...props.histories].sort((first, second) => {
        const firstDate = first.status_date
            ? Date.parse(first.status_date)
            : Number.NEGATIVE_INFINITY;
        const secondDate = second.status_date
            ? Date.parse(second.status_date)
            : Number.NEGATIVE_INFINITY;

        return (
            firstDate - secondDate ||
            String(first.id).localeCompare(String(second.id), undefined, {
                numeric: true,
            })
        );
    }),
);

function dateTimeLabel(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

function statusLabel(value: string | null): string {
    return value
        ? value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase())
        : '-';
}

function locationLabel(history: StatusHistory): string {
    return (
        [history.location, history.country].filter(Boolean).join(', ') ||
        'Location not recorded'
    );
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Status History</CardTitle>
            <CardDescription>{{ description }}</CardDescription>
        </CardHeader>
        <CardContent>
            <ol
                v-if="chronologicalHistories.length"
                class="flex flex-col"
            >
                <li
                    v-for="(history, historyIndex) in chronologicalHistories"
                    :key="history.id"
                    class="relative grid grid-cols-[1.25rem_minmax(0,1fr)] gap-x-4 pb-8 last:pb-0"
                >
                    <span
                        v-if="historyIndex < chronologicalHistories.length - 1"
                        class="absolute top-5 bottom-0 left-[0.5625rem] w-px bg-border"
                        aria-hidden="true"
                    />
                    <span
                        class="relative z-10 mt-1 flex size-5 items-center justify-center rounded-full border-2 border-primary bg-card"
                        aria-hidden="true"
                    >
                        <span class="size-2 rounded-full bg-primary" />
                    </span>
                    <div class="min-w-0">
                        <div
                            class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1"
                        >
                            <div>
                                <Badge
                                    v-if="history.status_type"
                                    variant="outline"
                                >
                                    {{
                                        history.status_type === 'document'
                                            ? 'Status dokumen'
                                            : 'Status shipment'
                                    }}
                                </Badge>
                                <p class="mt-1 font-medium">
                                    {{ statusLabel(history.status) }}
                                </p>
                            </div>
                            <time class="text-xs text-muted-foreground">
                                {{ dateTimeLabel(history.status_date) }}
                            </time>
                        </div>
                        <p
                            v-if="!history.status_type"
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{ locationLabel(history) }}
                        </p>
                        <p
                            v-if="history.remarks"
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{ history.remarks }}
                        </p>
                    </div>
                </li>
            </ol>
            <p
                v-else
                class="py-4 text-center text-sm text-muted-foreground"
            >
                {{ emptyMessage }}
            </p>
        </CardContent>
    </Card>
</template>
