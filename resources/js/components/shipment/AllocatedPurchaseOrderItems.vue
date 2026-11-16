<script setup lang="ts">
import { computed } from 'vue';
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

interface AllocatedItem {
    id: number | string;
    product_code: string | null;
    product_name: string | null;
    quantity: number | string;
    unit: string | null;
    container_number?: string | null;
}

const props = withDefaults(
    defineProps<{
        items: AllocatedItem[];
        description?: string;
        emptyMessage?: string;
    }>(),
    {
        description: '',
        emptyMessage: 'No allocated PO items.',
    },
);

const hasContainerNumbers = computed(() =>
    props.items.some((item) => item.container_number),
);
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Allocated PO items</CardTitle>
            <CardDescription v-if="description">
                {{ description }}
            </CardDescription>
        </CardHeader>
        <CardContent class="p-0">
            <Table class="min-w-[600px]">
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-if="hasContainerNumbers"
                            class="p-4"
                        >
                            Container
                        </TableHead>
                        <TableHead class="p-4">Product</TableHead>
                        <TableHead class="p-4">Quantity</TableHead>
                        <TableHead class="p-4">Unit</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="item in items"
                        :key="item.id"
                    >
                        <TableCell
                            v-if="hasContainerNumbers"
                            class="p-4"
                        >
                            {{ item.container_number || '-' }}
                        </TableCell>
                        <TableCell class="p-4">
                            {{ item.product_code || '-' }} —
                            {{ item.product_name || '-' }}
                        </TableCell>
                        <TableCell class="p-4">
                            {{ item.quantity }}
                        </TableCell>
                        <TableCell class="p-4">
                            {{ item.unit || '-' }}
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="!items.length">
                        <TableCell
                            :colspan="hasContainerNumbers ? 4 : 3"
                            class="p-6 text-center text-muted-foreground"
                        >
                            {{ emptyMessage }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
