<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

interface PurchaseOrder {
    id: number
    po_number: string
    supplier_id: number | string | null
    category: string | null
    po_date: string | null
    currency: string | null
    exchange_rate: number | string | null
    incoterm: string | null
    payment_term: string | null
    delivery_date: string | null
    arrival_date: string | null
    status: string | null
}
const props = defineProps<{
    purchaseOrders?: PurchaseOrder[]
}>()

const search = ref('')

const filteredPurchaseOrders = computed(() => {
    const purchaseOrders = props.purchaseOrders ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return purchaseOrders
    }

    return purchaseOrders.filter((purchaseOrder) => {
        return [
            purchaseOrder.id,
            purchaseOrder.po_number,
            purchaseOrder.supplier_id,
            purchaseOrder.po_date,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})
const statusClass = (status: string | null) => {
    switch (status?.toUpperCase()) {
        case 'COMPLETED':
        case 'DELIVERED':
        case 'CUSTOMS_CLEARED':
            return 'bg-green-100 text-green-700'
        case 'CANCELLED':
            return 'bg-red-100 text-red-700'
        case 'BOOKED':
        case 'IN_TRANSIT':
            return 'bg-blue-100 text-blue-700'
        case 'ON_HOLD':
            return 'bg-yellow-100 text-yellow-700'
        default:
            return 'bg-gray-100 text-gray-700'
    }
}

const statusLabel = (status: string | null) =>
    status
        ? status
            .toLowerCase()
            .split('_')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ')
        : '-'

const dateLabel = (date: string | null) => date ? date.slice(0, 10) : '-'

</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Purchase Orders
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor purchase orders.
                </p>
            </div>

            <Button as-child>
                <Link href="/po/create">
                    Add Purchase Order
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search purchase orders..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your purchase orders.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Purchase Order Number</TableHead>
                        <TableHead>Supplier</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>PO Date</TableHead>
                        <TableHead>Currency</TableHead>
                        <TableHead>Rate</TableHead>
                        <TableHead>Incoterm</TableHead>
                        <TableHead>TOP</TableHead>
                        <TableHead>Estimated Delivery Date</TableHead>
                        <TableHead>Estimated Arrival Date</TableHead>
                        <TableHead>Status</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredPurchaseOrders.length > 0">

                        <TableRow v-for="(purchaseOrder, index) in filteredPurchaseOrders" :key="purchaseOrder.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.po_number || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.supplier_id || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.category || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ dateLabel(purchaseOrder.po_date ?? null) }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.currency || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.exchange_rate || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.incoterm || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ purchaseOrder.payment_term || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ dateLabel(purchaseOrder.delivery_date ?? null) }}
                            </TableCell>

                            <TableCell>
                                {{ dateLabel(purchaseOrder.arrival_date ?? null) }}
                            </TableCell>

                            <TableCell>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                    :class="statusClass(purchaseOrder.status ?? null)">
                                    {{ statusLabel(purchaseOrder.status ?? null) }}
                                </span>
                            </TableCell>

                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            ⋮
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>

                                        <DropdownMenuSeparator />

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/po/${purchaseOrder.id}`">
                                                View
                                            </Link>
                                            </DropdownMenuItem>

                                            <DropdownMenuItem as-child>
                                                <Link :href="`/po/${purchaseOrder.id}/edit`">
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>

                    </template>

                    <!-- DATA KOSONG -->
                    <TableRow v-else>
                        <TableCell :colspan="13" class="h-32 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <p class="font-medium text-muted-foreground">
                                    {{ search.trim() ? 'No purchase orders match your search criteria.' : 'No Purchase Orders found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no Purchase Orders to display.'
                                    }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredPurchaseOrders.length }}
            purchase orders.
        </div>

    </div>
</template>

<style>
.greeting {
    color: red;
    font-weight: bold;
}
</style>