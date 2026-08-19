<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
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

interface Shipment {
    id: number | string

    shipment_number?: string | null
    shipment_date?: string | null

    bl_number?: string | null
    vessel_name?: string | null
    voyage_number?: string | null

    etd?: string | null
    eta?: string | null

    status?: string | null
    tracking_status?: string | null
}

/*
|--------------------------------------------------------------------------
| Props dari Laravel Controller
|--------------------------------------------------------------------------
*/

const props = defineProps<{
    shipments?: Shipment[]
}>()

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const search = ref('')

/*
|--------------------------------------------------------------------------
| Filtered Shipments
|--------------------------------------------------------------------------
*/

const filteredShipments = computed(() => {
    const shipments = props.shipments ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return shipments
    }

    return shipments.filter((shipment) => {
        return [
            shipment.shipment_number,
            shipment.bl_number,
            shipment.vessel_name,
            shipment.voyage_number,
            shipment.status,
            shipment.tracking_status,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

function statusClass(status?: string | null) {
    switch (status?.toLowerCase()) {
        case 'success':
        case 'completed':
        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'processing':
        case 'in_transit':
        case 'in transit':
            return 'bg-blue-100 text-blue-700'

        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'failed':
        case 'cancelled':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}
</script>
<template>
    <div class="w-full space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Shipment
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor shipment data.
                </p>
            </div>

            <Button as-child>
                <Link href="/shipment/create">
                    Add Shipment
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input
                v-model="search"
                type="search"
                placeholder="Search shipment..."
                class="max-w-sm"
            />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your shipments.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Shipment Number</TableHead>
                        <TableHead>BL Number</TableHead>
                        <TableHead>Vessel</TableHead>
                        <TableHead>Voyage</TableHead>
                        <TableHead>ETD</TableHead>
                        <TableHead>ETA</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Tracking</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredShipments.length > 0">

                        <TableRow
                            v-for="(shipment, index) in filteredShipments"
                            :key="shipment.id"
                        >
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell class="font-medium">
                                {{ shipment.shipment_number || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shipment.bl_number || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shipment.vessel_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shipment.voyage_number || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shipment.etd || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shipment.eta || '-' }}
                            </TableCell>

                            <TableCell>
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                    :class="statusClass(shipment.status)"
                                >
                                    {{ shipment.status || '-' }}
                                </span>
                            </TableCell>

                            <TableCell>
                                {{ shipment.tracking_status || '-' }}
                            </TableCell>

                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                        >
                                            ⋮
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>

                                        <DropdownMenuSeparator />

                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/shipment/${shipment.id}`"
                                            >
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/shipment/${shipment.id}/edit`"
                                            >
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
                        <TableCell
                            :colspan="10"
                            class="h-32 text-center"
                        >
                            <div class="flex flex-col items-center justify-center gap-2">
                                <p class="font-medium text-muted-foreground">
                                    No shipment found.
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    There are currently no shipment records.
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredShipments.length }}
            shipment(s).
        </div>

    </div>
</template>