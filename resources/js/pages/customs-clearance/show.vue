<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'
import { store as storeResponse } from '@/actions/App/Http/Controllers/CustomsClearanceResponsesController'
import { show as showResponse } from '@/actions/App/Http/Controllers/CustomsClearanceResponsesController'
import { index as clearanceIndex, update as updateClearance } from '@/actions/App/Http/Controllers/CustomsClearancesController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

interface ResponseType {
    id: number
    code: string | null
    name: string | null
}

interface CustomsResponse {
    id: number
    document_number: string | null
    response_date: string
    action_required: string | null
    action_taken: string | null
    remarks: string | null
    response_type: ResponseType | null
}

interface Clearance {
    id: number
    pib_number: string | null
    pib_date: string | null
    customs_channel: string | null
    customs_status: string | null
    inspection_start: string | null
    inspection_end: string | null
    remarks: string | null
    responses: CustomsResponse[]
}

interface Shipment {
    id: number
    shipment_number: string
    invoice_number: string | null
    bl_number: string | null
}

const props = defineProps<{
    shipment: Shipment
    clearance: Clearance | null
    responseTypes: ResponseType[]
}>()

const clearanceForm = useForm({
    pib_number: props.clearance?.pib_number ?? '',
    pib_date: props.clearance?.pib_date?.slice(0, 10) ?? '',
    customs_channel: props.clearance?.customs_channel ?? '',
    customs_status: props.clearance?.customs_status ?? '',
    inspection_start: props.clearance?.inspection_start?.replace(' ', 'T').slice(0, 16) ?? '',
    inspection_end: props.clearance?.inspection_end?.replace(' ', 'T').slice(0, 16) ?? '',
    remarks: props.clearance?.remarks ?? '',
})

const responseForm = useForm({
    customs_response_type_id: '',
    document_number: '',
    response_date: '',
    action_required: '',
    action_taken: '',
    attachment: null as File | null,
    remarks: '',
})

function saveClearance(): void {
    clearanceForm.put(updateClearance.url(props.shipment.id), {
        preserveScroll: true,
    })
}

function saveResponse(): void {
    if (!props.clearance) {
        return
    }

    responseForm.post(storeResponse.url(props.clearance.id), {
        preserveScroll: true,
        onSuccess: () => responseForm.reset(),
    })
}

function setAttachment(event: Event): void {
    const input = event.target as HTMLInputElement
    responseForm.attachment = input.files?.[0] ?? null
}
</script>

<template>
    <Head :title="`Customs Clearance - ${shipment.shipment_number}`" />

    <div class="mx-auto w-full space-y-6 p-4 sm:p-8">
        <div class="space-y-3">
            <Link :href="clearanceIndex.url()" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" /> Back to customs clearance
            </Link>
            <div>
                <h1 class="text-2xl font-semibold">PIB clearance · {{ shipment.shipment_number }}</h1>
                <p class="text-sm text-muted-foreground">
                    Shipment {{ shipment.shipment_number }} · Invoice {{ shipment.invoice_number || '-' }} · BL {{ shipment.bl_number || '-' }}
                </p>
            </div>
        </div>

        <form class="space-y-5 rounded-lg border bg-card p-5" @submit.prevent="saveClearance">
            <div>
                <h2 class="font-semibold">Customs Clearance</h2>
                <p class="text-sm text-muted-foreground">This shipment can have one PIB clearance. Responses are recorded separately as a history.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-2 text-sm font-medium">
                    PIB number
                    <Input v-model="clearanceForm.pib_number" />
                    <span v-if="clearanceForm.errors.pib_number" class="block text-xs text-destructive">{{ clearanceForm.errors.pib_number }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    PIB date
                    <Input v-model="clearanceForm.pib_date" type="date" />
                    <span v-if="clearanceForm.errors.pib_date" class="block text-xs text-destructive">{{ clearanceForm.errors.pib_date }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Customs channel
                    <select v-model="clearanceForm.customs_channel" class="h-10 w-full rounded-md border border-input bg-background px-3">
                        <option value="">Not set</option>
                        <option value="Green Line">Green Line</option>
                        <option value="Red Line">Red Line</option>
                    </select>
                    <span v-if="clearanceForm.errors.customs_channel" class="block text-xs text-destructive">{{ clearanceForm.errors.customs_channel }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Customs status
                    <Input v-model="clearanceForm.customs_status" />
                    <span v-if="clearanceForm.errors.customs_status" class="block text-xs text-destructive">{{ clearanceForm.errors.customs_status }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Inspection started
                    <Input v-model="clearanceForm.inspection_start" type="datetime-local" />
                    <span v-if="clearanceForm.errors.inspection_start" class="block text-xs text-destructive">{{ clearanceForm.errors.inspection_start }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Inspection completed
                    <Input v-model="clearanceForm.inspection_end" type="datetime-local" />
                    <span v-if="clearanceForm.errors.inspection_end" class="block text-xs text-destructive">{{ clearanceForm.errors.inspection_end }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium sm:col-span-2">
                    Remarks
                    <Textarea v-model="clearanceForm.remarks" rows="3" />
                    <span v-if="clearanceForm.errors.remarks" class="block text-xs text-destructive">{{ clearanceForm.errors.remarks }}</span>
                </label>
            </div>

            <Button type="submit" :disabled="clearanceForm.processing">
                {{ clearanceForm.processing ? 'Saving...' : 'Save PIB clearance' }}
            </Button>
        </form>

        <section class="space-y-4 rounded-lg border bg-card p-5">
            <div>
                <h2 class="font-semibold">Customs Clearance Responses</h2>
                <p class="text-sm text-muted-foreground">SPJM, SPPB, NPD, SPTNP, and other response documents form a history under this PIB.</p>
            </div>

            <p v-if="!clearance" class="rounded-md border border-dashed p-4 text-sm text-muted-foreground">
                Save the PIB clearance before adding its first response.
            </p>

            <form v-else class="grid gap-4 rounded-md border p-4 sm:grid-cols-2" @submit.prevent="saveResponse">
                <label class="space-y-2 text-sm font-medium">
                    Response type
                    <select v-model="responseForm.customs_response_type_id" required class="h-10 w-full rounded-md border border-input bg-background px-3">
                        <option value="" disabled>Select response type</option>
                        <option v-for="type in responseTypes" :key="type.id" :value="String(type.id)">
                            {{ type.code }} · {{ type.name }}
                        </option>
                    </select>
                    <span v-if="responseForm.errors.customs_response_type_id" class="block text-xs text-destructive">{{ responseForm.errors.customs_response_type_id }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Response date
                    <Input v-model="responseForm.response_date" type="date" required />
                    <span v-if="responseForm.errors.response_date" class="block text-xs text-destructive">{{ responseForm.errors.response_date }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Document number
                    <Input v-model="responseForm.document_number" />
                    <span v-if="responseForm.errors.document_number" class="block text-xs text-destructive">{{ responseForm.errors.document_number }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Attachment
                    <input type="file" class="block w-full text-sm" @change="setAttachment">
                    <span v-if="responseForm.errors.attachment" class="block text-xs text-destructive">{{ responseForm.errors.attachment }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Action required
                    <Textarea v-model="responseForm.action_required" rows="2" />
                    <span v-if="responseForm.errors.action_required" class="block text-xs text-destructive">{{ responseForm.errors.action_required }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium">
                    Action taken
                    <Textarea v-model="responseForm.action_taken" rows="2" />
                    <span v-if="responseForm.errors.action_taken" class="block text-xs text-destructive">{{ responseForm.errors.action_taken }}</span>
                </label>
                <label class="space-y-2 text-sm font-medium sm:col-span-2">
                    Remarks
                    <Textarea v-model="responseForm.remarks" rows="2" />
                    <span v-if="responseForm.errors.remarks" class="block text-xs text-destructive">{{ responseForm.errors.remarks }}</span>
                </label>
                <div class="sm:col-span-2">
                    <Button type="submit" :disabled="responseForm.processing || responseTypes.length === 0">
                        {{ responseForm.processing ? 'Saving...' : 'Add response to history' }}
                    </Button>
                    <p v-if="responseTypes.length === 0" class="mt-2 text-sm text-destructive">No response types are configured. Run the database seeder first.</p>
                </div>
            </form>

            <div v-if="clearance?.responses.length" class="overflow-x-auto rounded-md border">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="p-3 font-medium">Type</th>
                            <th class="p-3 font-medium">Document number</th>
                            <th class="p-3 font-medium">Date</th>
                            <th class="p-3 font-medium">Action taken</th>
                            <th class="p-3 font-medium">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="response in clearance.responses" :key="response.id" class="border-t">
                            <td class="p-3">{{ response.response_type?.code ?? response.response_type?.name ?? '-' }}</td>
                            <td class="p-3">{{ response.document_number || '-' }}</td>
                            <td class="p-3">{{ response.response_date.slice(0, 10) }}</td>
                            <td class="p-3">{{ response.action_taken || '-' }}</td>
                            <td class="p-3">
                                <Link :href="showResponse.url(response.id)" class="text-primary underline-offset-4 hover:underline">
                                    View response
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else-if="clearance" class="text-sm text-muted-foreground">No customs responses recorded for this PIB.</p>
        </section>
    </div>
</template>
