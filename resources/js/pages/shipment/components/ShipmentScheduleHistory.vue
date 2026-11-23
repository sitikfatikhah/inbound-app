<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import UpdateSchedule from '@/pages/shipment/forms/UpdateSchedule.vue'

interface Revision {
  id: number
  schedule_type: 'eta' | 'etd'
  previous_date: string | null
  new_date: string
  revision_reason: string
  created_at: string
}

defineProps<{
  shipmentId: number
  eta: string | null
  etd: string | null
  originalEta: string | null
  originalEtd: string | null
  histories: Revision[]
}>()

function dateLabel(value: string | null): string {
  return value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value)) : 'Not set'
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Schedule revision history</CardTitle>
      <CardDescription>Original and current dates are retained for schedule deviation analysis.</CardDescription>
    </CardHeader>
    <CardContent class="space-y-5">
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-md border p-3">
          <p class="text-sm text-muted-foreground">ETA</p>
          <p class="font-medium">Current: {{ dateLabel(eta) }}</p>
          <p class="text-sm text-muted-foreground">Initial: {{ dateLabel(originalEta) }}</p>
        </div>
        <div class="rounded-md border p-3">
          <p class="text-sm text-muted-foreground">ETD</p>
          <p class="font-medium">Current: {{ dateLabel(etd) }}</p>
          <p class="text-sm text-muted-foreground">Initial: {{ dateLabel(originalEtd) }}</p>
        </div>
      </div>

      <div v-if="histories.length" class="overflow-x-auto rounded-md border">
        <table class="w-full min-w-[640px] text-sm">
          <thead class="bg-muted/50 text-left">
            <tr><th class="p-3">Schedule</th><th class="p-3">Previous</th><th class="p-3">New</th><th class="p-3">Reason</th></tr>
          </thead>
          <tbody>
            <tr v-for="revision in histories" :key="revision.id" class="border-t">
              <td class="p-3 font-medium">{{ revision.schedule_type.toUpperCase() }}</td>
              <td class="p-3">{{ dateLabel(revision.previous_date) }}</td>
              <td class="p-3">{{ dateLabel(revision.new_date) }}</td>
              <td class="p-3">{{ revision.revision_reason }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="text-sm text-muted-foreground">No schedule revisions recorded.</p>

      <UpdateSchedule :shipment-id="shipmentId" :eta="eta" :etd="etd" />
    </CardContent>
  </Card>
</template>