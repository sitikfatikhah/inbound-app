<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'

interface StatusHistory {
  id: number
  status: string | null
  status_date: string | null
  remarks: string | null
}

interface Revision {
  id: number
  schedule_type: string
  previous_date: string | null
  new_date: string
  revision_reason: string
  created_at: string
}

interface ShipmentDocument {
  id: number
  document_name: string
  verification_status: string
  updated_at: string
}

const props = defineProps<{
  statusHistories: StatusHistory[]
  scheduleHistories: Revision[]
  documents: ShipmentDocument[]
  customsClearance: { jalur: string | null; updated_at: string } | null
}>()

const activities = computed(() => [
  ...props.statusHistories.map((item) => ({
    id: `status-${item.id}`,
    title: item.status ?? 'Tracking update',
    detail: item.remarks,
    at: item.status_date,
  })),
  ...props.scheduleHistories.map((item) => ({
    id: `schedule-${item.id}`,
    title: `${item.schedule_type.toUpperCase()} revised`,
    detail: `${item.previous_date?.slice(0, 10) ?? 'Not set'} → ${item.new_date.slice(0, 10)} · ${item.revision_reason}`,
    at: item.created_at,
  })),
  ...props.documents.map((item) => ({
    id: `document-${item.id}`,
    title: `${item.document_name}: ${item.verification_status}`,
    detail: null,
    at: item.updated_at,
  })),
  ...(props.customsClearance ? [{
    id: 'customs-clearance',
    title: `Customs channel: ${props.customsClearance.jalur ?? 'not set'}`,
    detail: null,
    at: props.customsClearance.updated_at,
  }] : []),
].sort((first, second) => Date.parse(second.at ?? '') - Date.parse(first.at ?? '')))

function dateTimeLabel(value: string | null): string {
  if (!value) {
return 'Date not recorded'
}

  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Shipment activity log</CardTitle>
      <CardDescription>Recent tracking, document, customs, and schedule events.</CardDescription>
    </CardHeader>
    <CardContent>
      <ul v-if="activities.length" class="divide-y">
        <li v-for="activity in activities" :key="activity.id" class="flex flex-wrap justify-between gap-3 py-3 first:pt-0 last:pb-0">
          <div>
            <p class="font-medium">{{ activity.title }}</p>
            <p v-if="activity.detail" class="mt-1 text-sm text-muted-foreground">{{ activity.detail }}</p>
          </div>
          <time class="text-xs text-muted-foreground">{{ dateTimeLabel(activity.at) }}</time>
        </li>
      </ul>
      <p v-else class="py-4 text-center text-sm text-muted-foreground">No activity recorded yet.</p>
    </CardContent>
  </Card>
</template>