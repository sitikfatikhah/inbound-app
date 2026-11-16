<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Milestone {
    id: number | string;
    milestone_name: string;
    milestone_date: string | null;
    planned_date: string | null;
    actual_date: string | null;
    status: string;
    remarks: string | null;
}

const props = withDefaults(
    defineProps<{
        milestones: Milestone[];
        description?: string;
        emptyMessage?: string;
    }>(),
    {
        description: 'Planned and actual shipment activities.',
        emptyMessage: 'No milestones recorded.',
    },
);

function dateLabel(value: string | null): string {
    return value?.slice(0, 10) ?? '-';
}

function statusLabel(value: string): string {
    return value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Milestones</CardTitle>
            <CardDescription>{{ props.description }}</CardDescription>
        </CardHeader>
        <CardContent class="p-0">
            <Table class="min-w-[700px]">
                <TableHeader>
                    <TableRow>
                        <TableHead class="p-4">Milestone</TableHead>
                        <TableHead class="p-4">Planned Date</TableHead>
                        <TableHead class="p-4">Actual Date</TableHead>
                        <TableHead class="p-4">Status</TableHead>
                        <TableHead class="p-4">Remarks</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="milestone in props.milestones"
                        :key="milestone.id"
                    >
                        <TableCell class="p-4 font-medium">
                            {{ milestone.milestone_name || '-' }}
                        </TableCell>
                        <TableCell class="p-4">
                            {{ dateLabel(milestone.planned_date) }}
                        </TableCell>
                        <TableCell class="p-4">
                            {{
                                dateLabel(
                                    milestone.actual_date ??
                                        milestone.milestone_date,
                                )
                            }}
                        </TableCell>
                        <TableCell class="p-4">
                            <Badge variant="outline">
                                {{ statusLabel(milestone.status) }}
                            </Badge>
                        </TableCell>
                        <TableCell class="p-4">
                            {{ milestone.remarks || '-' }}
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="!props.milestones.length">
                        <TableCell
                            colspan="5"
                            class="p-6 text-center text-muted-foreground"
                        >
                            {{ props.emptyMessage }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
