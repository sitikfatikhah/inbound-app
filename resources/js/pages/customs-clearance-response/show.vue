<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'
import { show as showClearance } from '@/actions/App/Http/Controllers/CustomsClearancesController'

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
    action_required: string | null
    action_taken: string | null
    attachment_path: string | null
    attachment_url: string | null
    remarks: string | null
    customs_clearance: Clearance
    response_type: ResponseType | null
}

const props = defineProps<{
    response: CustomsResponse
}>()
</script>

<template>
    <Head title="Customs Response" />

    <div class="mx-auto w-full max-w-4xl space-y-6 p-4 sm:p-8">
        <Link
            :href="showClearance.url(props.response.customs_clearance.shipment.id)"
            class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
        >
            <ArrowLeft class="h-4 w-4" /> Back to {{ props.response.customs_clearance.shipment.shipment_number }}
        </Link>

        <section class="space-y-5 rounded-lg border bg-card p-6">
            <div>
                <p class="text-sm text-muted-foreground">PIB {{ props.response.customs_clearance.pib_number || '-' }}</p>
                <h1 class="text-2xl font-semibold">
                    {{ props.response.response_type?.code ?? props.response.response_type?.name ?? 'Customs response' }}
                </h1>
            </div>

            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-muted-foreground">Shipment</dt>
                    <dd class="font-medium">{{ props.response.customs_clearance.shipment.shipment_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Response date</dt>
                    <dd class="font-medium">{{ props.response.response_date.slice(0, 10) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Document number</dt>
                    <dd class="font-medium">{{ props.response.document_number || '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Attachment</dt>
                    <dd>
                        <a v-if="props.response.attachment_url" :href="props.response.attachment_url" target="_blank" rel="noopener noreferrer" class="text-primary underline-offset-4 hover:underline">
                            Download attachment
                        </a>
                        <span v-else>-</span>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm text-muted-foreground">Action required</dt>
                    <dd class="whitespace-pre-wrap font-medium">{{ props.response.action_required || '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm text-muted-foreground">Action taken</dt>
                    <dd class="whitespace-pre-wrap font-medium">{{ props.response.action_taken || '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm text-muted-foreground">Remarks</dt>
                    <dd class="whitespace-pre-wrap font-medium">{{ props.response.remarks || '-' }}</dd>
                </div>
            </dl>
        </section>
    </div>
</template>
