<script setup lang="ts">
import { computed } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'

interface Shipment {
  shipment_number: string | null
  tracking_status: string | null
  etd: string | null
  eta: string | null
  original_eta: string | null
  atd: string | null
  ata: string | null
  customs_clearance?: { jalur: string | null; sppb_date: string | null } | null
}

const props = defineProps<{
  shipment: Shipment
  documentCompleteness: number
}>()

const status = computed(() => props.shipment.tracking_status?.replaceAll('_', ' ') ?? 'Not set')
const completionLabel = computed(() => `${props.documentCompleteness}%`)

function dateLabel(value: string | null): string {
  if (!value) {
return 'Not set'
}

  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value))
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Shipment overview</CardTitle>
      <CardDescription>Current progress, customs status, and schedule at a glance.</CardDescription>
    </CardHeader>
    <CardContent class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-lg border p-4">
        <p class="text-sm text-muted-foreground">Tracking status</p>
        <Badge class="mt-2 capitalize" variant="outline">{{ status }}</Badge>
      </div>
      <div class="rounded-lg border p-4">
        <p class="text-sm text-muted-foreground">Document completeness</p>
        <p class="mt-2 text-2xl font-semibold">{{ completionLabel }}</p>
      </div>
      <div class="rounded-lg border p-4">
        <p class="text-sm text-muted-foreground">ETA</p>
        <p class="mt-2 font-semibold">{{ dateLabel(shipment.eta) }}</p>
        <p v-if="shipment.original_eta" class="mt-1 text-xs text-muted-foreground">Initial: {{ dateLabel(shipment.original_eta) }}</p>
      </div>
      <div class="rounded-lg border p-4">
        <p class="text-sm text-muted-foreground">Customs clearance</p>
        <p class="mt-2 font-semibold">{{ shipment.customs_clearance?.jalur ?? 'Not submitted' }}</p>
        <p class="mt-1 text-xs text-muted-foreground">
          {{ shipment.customs_clearance?.sppb_date ? `SPPB ${dateLabel(shipment.customs_clearance.sppb_date)}` : 'SPPB not recorded' }}
        </p>
      </div>
    </CardContent>
  </Card>
</template>