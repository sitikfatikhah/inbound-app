<script setup lang="ts">
import { computed } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import UpdateCustomsStatus from '@/pages/shipment/forms/UpdateCustomsStatus.vue'

interface CustomsClearance {
  jalur: 'Hijau' | 'Merah' | null
  pib_number: string | null
  pib_date: string | null
  sppb_number: string | null
  sppb_date: string | null
  inspection_start: string | null
  inspection_end: string | null
  inspection_duration: number | null
  remarks: string | null
}

const props = defineProps<{
  shipmentId: number
  clearance: CustomsClearance | null
  inspectionSlaHours: number
}>()

const inspectionOverdue = computed(() => {
  const start = props.clearance?.inspection_start

  return Boolean(start && !props.clearance?.inspection_end
    && Date.now() - Date.parse(start) > props.inspectionSlaHours * 60 * 60 * 1000)
})

function dateLabel(value: string | null): string {
  return value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value)) : 'Not recorded'
}

function dateTimeLabel(value: string | null): string {
  return value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)) : 'Not recorded'
}
</script>

<template>
  <Card>
    <CardHeader>
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <CardTitle>Customs clearance</CardTitle>
          <CardDescription>PIB submission, CEISA channel, inspection, and SPPB.</CardDescription>
        </div>
        <Badge v-if="clearance?.jalur" :variant="clearance.jalur === 'Merah' ? 'destructive' : 'outline'">
          Jalur {{ clearance.jalur }}
        </Badge>
      </div>
    </CardHeader>
    <CardContent class="space-y-5">
      <div v-if="clearance?.jalur === 'Merah'" class="rounded-md border border-destructive/40 bg-destructive/5 p-3 text-sm" role="status">
        <strong>Critical:</strong> Shipment mendapat Jalur Merah.
      </div>
      <div v-if="inspectionOverdue" class="rounded-md border border-amber-500/40 bg-amber-500/5 p-3 text-sm" role="status">
        <strong>Warning:</strong> Pemeriksaan belum selesai melewati target {{ inspectionSlaHours }} jam.
      </div>

      <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div><dt class="text-sm text-muted-foreground">PIB</dt><dd class="font-medium">{{ clearance?.pib_number || 'Not submitted' }}</dd><dd class="text-sm text-muted-foreground">{{ dateLabel(clearance?.pib_date ?? null) }}</dd></div>
        <div><dt class="text-sm text-muted-foreground">Inspection started</dt><dd class="font-medium">{{ dateTimeLabel(clearance?.inspection_start ?? null) }}</dd></div>
        <div><dt class="text-sm text-muted-foreground">Inspection completed</dt><dd class="font-medium">{{ dateTimeLabel(clearance?.inspection_end ?? null) }}</dd></div>
        <div><dt class="text-sm text-muted-foreground">Inspection duration</dt><dd class="font-medium">{{ clearance?.inspection_duration == null ? 'Not completed' : `${(clearance.inspection_duration / 60).toFixed(1)} hours` }}</dd></div>
        <div><dt class="text-sm text-muted-foreground">SPPB</dt><dd class="font-medium">{{ clearance?.sppb_number || 'Not issued' }}</dd><dd class="text-sm text-muted-foreground">{{ dateLabel(clearance?.sppb_date ?? null) }}</dd></div>
      </dl>

      <p v-if="clearance?.remarks" class="text-sm text-muted-foreground">{{ clearance.remarks }}</p>
      <UpdateCustomsStatus :shipment-id="shipmentId" :clearance="clearance" />
    </CardContent>
  </Card>
</template>