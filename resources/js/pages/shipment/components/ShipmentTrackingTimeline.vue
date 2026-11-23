<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'

interface StatusHistory {
  id: number
  status: string | null
  status_date: string | null
  location: string | null
  country: string | null
  remarks: string | null
}

interface Milestone {
  id: number
  milestone_name: string
  status: string
  planned_date: string | null
  actual_date: string | null
  remarks: string | null
}

const props = defineProps<{
  histories: StatusHistory[]
  milestones: Milestone[]
}>()

const events = computed(() => [
  ...props.histories.map((history) => ({
    id: `status-${history.id}`,
    title: history.status ?? 'Status updated',
    date: history.status_date,
    detail: [history.location, history.country, history.remarks].filter(Boolean).join(' · '),
  })),
  ...props.milestones.map((milestone) => ({
    id: `milestone-${milestone.id}`,
    title: milestone.milestone_name,
    date: milestone.actual_date ?? milestone.planned_date,
    detail: [milestone.status, milestone.remarks].filter(Boolean).join(' · '),
  })),
].sort((first, second) => {
  const firstTime = first.date ? Date.parse(first.date) : Number.POSITIVE_INFINITY
  const secondTime = second.date ? Date.parse(second.date) : Number.POSITIVE_INFINITY

  return firstTime - secondTime
}))

function dateLabel(value: string | null): string {
  return value
    ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value))
    : 'Date not recorded'
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Tracking timeline</CardTitle>
      <CardDescription>Shipment status updates and operational milestones.</CardDescription>
    </CardHeader>
    <CardContent>
      <ol v-if="events.length" class="space-y-5">
        <li v-for="event in events" :key="event.id" class="grid grid-cols-[1rem_minmax(0,1fr)] gap-3">
          <span class="mt-1.5 size-3 rounded-full bg-primary" aria-hidden="true" />
          <div class="min-w-0">
            <div class="flex flex-wrap justify-between gap-2">
              <p class="font-medium">{{ event.title }}</p>
              <time class="text-xs text-muted-foreground">{{ dateLabel(event.date) }}</time>
            </div>
            <p v-if="event.detail" class="mt-1 text-sm text-muted-foreground">{{ event.detail }}</p>
          </div>
        </li>
      </ol>
      <p v-else class="py-4 text-center text-sm text-muted-foreground">No tracking activity recorded yet.</p>
    </CardContent>
  </Card>
</template>