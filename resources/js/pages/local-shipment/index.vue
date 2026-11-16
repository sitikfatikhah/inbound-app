<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    create,
    destroy,
    edit,
    show,
} from '@/actions/App/Http/Controllers/LocalShipmentsController';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

interface LocalShipment {
    id: number;
    shipment_number: string;
    delivery_order_number: string | null;
    etd: string | null;
    atd: string | null;
    status_document: string | null;
    tracking_status: string | null;
    purchase_orders: Array<{
        po_number: string;
        supplier?: { supplier_name: string } | null;
    }>;
    purchase_order?: {
        po_number: string;
        supplier?: { supplier_name: string } | null;
    } | null;
    warehouse?: { warehouse_name: string } | null;
    status_histories_count: number;
}

const props = defineProps<{ localShipments: LocalShipment[] }>();
const search = ref('');

const filteredShipments = computed(() => {
    const keyword = search.value.trim().toLocaleLowerCase();

    if (!keyword) {
        return props.localShipments;
    }

    return props.localShipments.filter((shipment) =>
        [
            shipment.shipment_number,
            shipment.purchase_orders.map((purchaseOrder) => purchaseOrder.po_number).join(', '),
            shipment.purchase_orders.map((purchaseOrder) => purchaseOrder.supplier?.supplier_name).join(', '),
            shipment.purchase_order?.po_number,
            shipment.purchase_order?.supplier?.supplier_name,
            shipment.warehouse?.warehouse_name,
            shipment.delivery_order_number,
            shipment.tracking_status,
            shipment.status_document,
        ].some((value) => value?.toLocaleLowerCase().includes(keyword)),
    );
});

function dateLabel(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
    }).format(new Date(value));
}

function deleteShipment(shipment: LocalShipment) {
    if (
        window.confirm(
            `Hapus ${shipment.shipment_number} beserta riwayat statusnya?`,
        )
    ) {
        router.delete(destroy.url(shipment.id));
    }
}
</script>

<template>
    <Head title="Local Shipment" />

    <main class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold">Local Shipment</h1>
                <p class="text-sm text-muted-foreground">
                    Manage local shipments and their status history.
                </p>
            </div>
            <Link
                :href="create.url()"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90"
            >
                Create Shipment
            </Link>
        </header>

        <label class="block max-w-md space-y-2 text-sm font-medium">
            Search Shipment
            <input
                v-model="search"
                type="search"
                placeholder="Number, PO, vendor, warehouse..."
                class="h-10 w-full rounded-md border bg-background px-3 font-normal"
            />
        </label>

        <div class="overflow-x-auto rounded-xl border bg-card">
            <Table class="w-full min-w-[900px] text-left text-sm">
                <TableHeader class="border-b bg-muted/40 text-muted-foreground">
                    <TableRow>
                        <TableHead class="p-4 font-medium">Shipment</TableHead>
                        <TableHead class="p-4 font-medium">PO / Vendor</TableHead>
                        <TableHead class="p-4 font-medium">Warehouse</TableHead>
                        <TableHead class="p-4 font-medium">ETD / ATD</TableHead>
                        <TableHead class="p-4 font-medium">Tracking</TableHead>
                        <TableHead class="p-4 font-medium">History</TableHead>
                        <TableHead class="p-4 font-medium">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="shipment in filteredShipments"
                        :key="shipment.id"
                        class="border-b last:border-0"
                    >
                        <TableCell class="p-4">
                            <Link
                                :href="show.url(shipment.id)"
                                class="font-semibold text-primary hover:underline"
                                >{{ shipment.shipment_number }}</Link
                            >
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    shipment.delivery_order_number ||
                                    'Without delivery order number'
                                }}
                            </p>
                        </TableCell>
                        <TableCell class="p-4">
                            <p>
                                {{ shipment.purchase_orders.map((purchaseOrder) => purchaseOrder.po_number).join(', ') || shipment.purchase_order?.po_number || '-' }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    shipment.purchase_orders.map((purchaseOrder) => purchaseOrder.supplier?.supplier_name).filter(Boolean).join(', ') || shipment.purchase_order?.supplier?.supplier_name || '-'
                                }}
                            </p>
                        </TableCell>
                        <TableCell class="p-4">
                            {{ shipment.warehouse?.warehouse_name || '-' }}
                        </TableCell>
                        <TableCell class="p-4">
                            <p>ETD {{ dateLabel(shipment.etd) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                ATD {{ dateLabel(shipment.atd) }}
                            </p>
                        </TableCell>
                        <TableCell class="p-4">
                            <p class="font-medium">
                                {{ shipment.tracking_status || '-' }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ shipment.status_document || '-' }}
                            </p>
                        </TableCell>
                        <TableCell class="p-4">{{ shipment.status_histories_count }}</TableCell>
                        <TableCell>
                            <DropdownMenu class="pl-4">
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            ⋮
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end" side="bottom" :side-offset="4">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>

                                        <DropdownMenuSeparator />

                                        <DropdownMenuItem as-child>
                                            <Link :href="show.url(shipment.id)">
                                                View
                                            </Link>
                                            </DropdownMenuItem>

                                            <DropdownMenuItem as-child>
                                                <Link :href="edit.url(shipment.id)">
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="!filteredShipments.length">
                        <TableCell
                            colspan="7"
                            class="p-10 text-center text-muted-foreground"
                        >
                            {{
                                search
                                    ? 'Sorry, no shipments found.'
                                    : 'Sorry, no shipments available.'
                            }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <p class="text-sm text-muted-foreground">
            Menampilkan {{ filteredShipments.length }} dari
            {{ localShipments.length }} shipment.
        </p>
    </main>
</template>
