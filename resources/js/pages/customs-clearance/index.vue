<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { show as showClearance } from '@/actions/App/Http/Controllers/CustomsClearancesController'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

interface ResponseSummary {
    id: number
}

interface Clearance {
    id: number
    pib_number: string | null
    customs_channel: string | null
    responses: ResponseSummary[]
}

interface Shipment {
    id: number
    shipment_number: string
    invoice_number: string | null
    bl_number: string | null
    eta: string | null
    pib_clearance: Clearance | null
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

const props = defineProps<{
    shipments: {
        data: Shipment[]
        links: PaginationLink[]
    }
}>()

const dateLabel = (value: string | null) => value?.slice(0, 10) ?? '-'
const paginationLabel = (label: string) => {
    if (label.includes('Previous')) {
        return 'Previous'
    }

    if (label.includes('Next')) {
        return 'Next'
    }

    return label
}
</script>

<template>
    <Head title="Customs Clearance" />

    <div class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <div>
            <h1 class="text-2xl font-semibold">Customs Clearance</h1>
            <p class="text-sm text-muted-foreground">One PIB clearance and its response history per shipment.</p>
        </div>

        <div class="overflow-x-auto rounded-md border">
            <Table>
                <TableCaption>Shipments and their PIB customs clearance.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Shipment</TableHead>
                        <TableHead>Invoice</TableHead>
                        <TableHead>Bill of lading</TableHead>
                        <TableHead>ETA</TableHead>
                        <TableHead>PIB number</TableHead>
                        <TableHead>Channel</TableHead>
                        <TableHead>Responses</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="shipment in props.shipments.data" :key="shipment.id">
                        <TableCell class="font-medium">{{ shipment.shipment_number }}</TableCell>
                        <TableCell>{{ shipment.invoice_number || '-' }}</TableCell>
                        <TableCell>{{ shipment.bl_number || '-' }}</TableCell>
                        <TableCell>{{ dateLabel(shipment.eta) }}</TableCell>
                        <TableCell>{{ shipment.pib_clearance?.pib_number || 'Not recorded' }}</TableCell>
                        <TableCell>{{ shipment.pib_clearance?.customs_channel || '-' }}</TableCell>
                        <TableCell>{{ shipment.pib_clearance?.responses.length ?? 0 }}</TableCell>
                        <TableCell class="text-right">
                            <Button as-child variant="outline" size="sm">
                                <Link :href="showClearance.url(shipment.id)">
                                    {{ shipment.pib_clearance ? 'View / update PIB' : 'Record PIB' }}
                                </Link>
                            </Button>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="props.shipments.data.length === 0">
                        <TableCell colspan="8" class="py-8 text-center text-muted-foreground">
                            No shipments found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <nav v-if="props.shipments.links.length > 3" class="flex flex-wrap gap-2" aria-label="Pagination">
            <Link
                v-for="link in props.shipments.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :preserve-scroll="true"
                class="rounded-md border px-3 py-2 text-sm"
                :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-50': !link.url }"
            >{{ paginationLabel(link.label) }}</Link>
        </nav>
    </div>
</template>
