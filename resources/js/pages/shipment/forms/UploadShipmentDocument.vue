<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { store as storeShipmentDocument, update as updateShipmentDocument } from '@/actions/App/Http/Controllers/ShipmentDocumentController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

interface ShipmentDocument {
  id: number
  document_file: string | null
  document_url: string | null
  document_number: string | null
  document_date: string | null
  verification_status: 'missing' | 'pending' | 'complete' | 'deficient'
  verification_notes: string | null
}

const props = defineProps<{
  shipmentId: number
  documentType: string
  documentName: string
  document: ShipmentDocument | null
}>()

const form = useForm({
  shipment_id: props.shipmentId,
  document_type: props.documentType,
  document_name: props.documentName,
  document_file: null as File | null,
})

const reviewForm = useForm({
  verification_status: 'complete' as 'complete' | 'deficient',
  verification_notes: props.document?.verification_notes ?? '',
})

function submitUpload(): void {
  if (props.document) {
    form.put(updateShipmentDocument.url(props.document.id), {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => form.reset('document_file'),
    })

    return
  }

  form.post(storeShipmentDocument.url(), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => form.reset('document_file'),
  })
}

function submitReview(status: 'complete' | 'deficient'): void {
  if (!props.document) {
    return
  }

  reviewForm.verification_status = status
  reviewForm.put(updateShipmentDocument.url(props.document.id), {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="space-y-3">
    <div v-if="props.document?.document_url" class="flex flex-wrap items-center gap-3 text-sm">
      <a :href="props.document.document_url" target="_blank" rel="noreferrer" class="text-primary underline underline-offset-4">
        View uploaded document
      </a>
      <span v-if="props.document.document_number" class="text-muted-foreground">No. {{ props.document.document_number }}</span>
      <span v-if="props.document.document_date" class="text-muted-foreground">{{ props.document.document_date.slice(0, 10) }}</span>
    </div>

    <form class="flex flex-col gap-3 sm:flex-row sm:items-end" @submit.prevent="submitUpload">
      <label class="min-w-0 flex-1 space-y-2 text-sm font-medium">
        {{ props.document ? 'Replace file' : `Upload ${props.documentName}` }}
        <Input
          type="file"
          accept=".pdf,.jpg,.jpeg,.png,.xlsx,.doc,.docx"
          @change="form.document_file = ($event.target as HTMLInputElement).files?.[0] ?? null"
        />
        <span v-if="form.errors.document_file" class="block text-xs text-destructive">{{ form.errors.document_file }}</span>
      </label>
      <Button type="submit" :disabled="form.processing || !form.document_file">
        {{ form.processing ? 'Uploading...' : 'Upload' }}
      </Button>
    </form>

    <div v-if="form.progress" class="space-y-1">
      <div class="flex justify-between text-xs text-muted-foreground">
        <span>Upload progress</span>
        <span>{{ form.progress.percentage }}%</span>
      </div>
      <div class="h-2 overflow-hidden rounded-full bg-muted">
        <div class="h-full bg-primary transition-all" :style="{ width: `${form.progress.percentage}%` }" />
      </div>
    </div>

    <form v-if="props.document?.document_file" class="space-y-3 border-t pt-3" @submit.prevent="submitReview(reviewForm.verification_status)">
      <label class="space-y-2 text-sm font-medium">
        Verification notes
        <Textarea v-model="reviewForm.verification_notes" rows="2" />
        <span v-if="reviewForm.errors.verification_notes" class="block text-xs text-destructive">{{ reviewForm.errors.verification_notes }}</span>
      </label>
      <p v-if="reviewForm.errors.verification_status" class="text-xs text-destructive">{{ reviewForm.errors.verification_status }}</p>
      <div class="flex flex-wrap gap-2">
        <Button type="button" size="sm" :disabled="reviewForm.processing" @click="submitReview('complete')">
          Mark complete
        </Button>
        <Button type="button" size="sm" variant="outline" :disabled="reviewForm.processing" @click="submitReview('deficient')">
          Mark deficient
        </Button>
      </div>
    </form>
  </div>
</template>