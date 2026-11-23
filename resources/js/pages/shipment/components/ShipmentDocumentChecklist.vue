<script setup lang="ts">
import { CheckCircle2, CircleAlert, CircleDashed } from 'lucide-vue-next'
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import UploadShipmentDocument from '@/pages/shipment/forms/UploadShipmentDocument.vue'

interface ShipmentDocument {
  id: number
  document_type: string
  document_name: string
  document_file: string | null
  document_url: string | null
  document_number: string | null
  document_date: string | null
  verification_status: 'missing' | 'pending' | 'complete' | 'deficient'
  verification_notes: string | null
}

const props = defineProps<{
  shipmentId: number
  documents: ShipmentDocument[]
  documentTypes: Record<string, string>
  requiredDocumentTypes: string[]
}>()

const checklist = computed(() => props.requiredDocumentTypes.map((type) => ({
  type,
  name: props.documentTypes[type] ?? type,
  document: props.documents.find((item) => item.document_type === type) ?? null,
})))

const completeCount = computed(() => checklist.value.filter(
  (item) => item.document?.verification_status === 'complete',
).length)
const completionPercentage = computed(() => checklist.value.length
  ? Math.round((completeCount.value / checklist.value.length) * 100)
  : 0)

function statusLabel(status?: ShipmentDocument['verification_status']): string {
  if (status === 'complete') {
return 'Complete'
}

  if (status === 'deficient') {
return 'Deficient'
}

  if (status === 'pending') {
return 'Awaiting verification'
}

  return 'Missing'
}
</script>

<template>
  <Card>
    <CardHeader>
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <CardTitle>Required document checklist</CardTitle>
          <CardDescription>Upload each import document and mark it complete after verification.</CardDescription>
        </div>
        <div class="min-w-36">
          <div class="flex justify-between text-sm">
            <span>Complete</span>
            <strong>{{ completionPercentage }}%</strong>
          </div>
          <div class="mt-2 h-2 overflow-hidden rounded-full bg-muted">
            <div class="h-full bg-primary transition-all" :style="{ width: `${completionPercentage}%` }" />
          </div>
          <p class="mt-1 text-xs text-muted-foreground">{{ completeCount }} of {{ checklist.length }} verified</p>
        </div>
      </div>
    </CardHeader>
    <CardContent class="space-y-4">
      <article v-for="item in checklist" :key="item.type" class="space-y-4 rounded-lg border p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <CheckCircle2 v-if="item.document?.verification_status === 'complete'" class="size-5 text-green-600" aria-hidden="true" />
            <CircleAlert v-else-if="item.document?.verification_status === 'deficient'" class="size-5 text-amber-600" aria-hidden="true" />
            <CircleDashed v-else class="size-5 text-muted-foreground" aria-hidden="true" />
            <div>
              <h3 class="font-medium">{{ item.name }}</h3>
              <p class="text-xs text-muted-foreground">{{ statusLabel(item.document?.verification_status) }}</p>
            </div>
          </div>
        </div>
        <p v-if="item.document?.verification_notes" class="rounded-md bg-muted/50 p-3 text-sm text-muted-foreground">
          {{ item.document.verification_notes }}
        </p>
        <UploadShipmentDocument
          :shipment-id="props.shipmentId"
          :document-type="item.type"
          :document-name="item.name"
          :document="item.document"
        />
      </article>
    </CardContent>
  </Card>
</template>