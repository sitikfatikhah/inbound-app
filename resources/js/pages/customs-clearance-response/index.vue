<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { show as showResponse } from '@/actions/App/Http/Controllers/CustomsClearanceResponsesController'
import { show as showClearance } from '@/actions/App/Http/Controllers/CustomsClearancesController'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

interface ResponseType {
    code: string | null
    name: string | null
}

interface Shipment {
    id: number
    shipment_number: string
}

interface Clearance {
    id: number
    pib_number: string | null
    shipment: Shipment
}

interface CustomsResponse {
    id: number
    response_date: string
    document_number: string | null
    action_taken: string | null
    customs_clearance: Clearance
    response_type: ResponseType | null
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

const props = defineProps<{
    responses: {
        data: CustomsResponse[]
        links: PaginationLink[]
    }
}>()

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
    <Head title="Customs Clearance Responses" />

    <div class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <div>
            <h1 class="text-2xl font-semibold">Customs Clearance Responses</h1>
            <p class="text-sm text-muted-foreground">Response and document history grouped under each PIB.</p>
        </div>

        <div class="overflow-x-auto rounded-md border">
            <Table>
                <TableCaption>Customs responses recorded for PIB clearances.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Shipment</TableHead>
                        <TableHead>PIB</TableHead>
                        <TableHead>Response type</TableHead>
                        <TableHead>Document number</TableHead>
                        <TableHead>Response date</TableHead>
                        <TableHead>Action taken</TableHead>
                        <TableHead>Details</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="response in props.responses.data" :key="response.id">
                        <TableCell class="font-medium">
                            <Link :href="showClearance.url(response.customs_clearance.shipment.id)" class="text-primary underline-offset-4 hover:underline">
                                {{ response.customs_clearance.shipment.shipment_number }}
                            </Link>
                        </TableCell>
                        <TableCell>{{ response.customs_clearance.pib_number || '-' }}</TableCell>
                        <TableCell>{{ response.response_type?.code ?? response.response_type?.name ?? '-' }}</TableCell>
                        <TableCell>{{ response.document_number || '-' }}</TableCell>
                        <TableCell>{{ response.response_date.slice(0, 10) }}</TableCell>
                        <TableCell>{{ response.action_taken || '-' }}</TableCell>
                        <TableCell>
                            <Link :href="showResponse.url(response.id)" class="text-primary underline-offset-4 hover:underline">
                                View
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="props.responses.data.length === 0">
                        <TableCell colspan="7" class="py-8 text-center text-muted-foreground">
                            No customs responses recorded.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <nav v-if="props.responses.links.length > 3" class="flex flex-wrap gap-2" aria-label="Pagination">
            <Link
                v-for="link in props.responses.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :preserve-scroll="true"
                class="rounded-md border px-3 py-2 text-sm"
                :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-50': !link.url }"
            >{{ paginationLabel(link.label) }}</Link>
        </nav>
    </div>
</template>
