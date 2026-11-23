<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { updateTrackingStatus } from '@/actions/App/Http/Controllers/ShipmentController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps<{
  shipmentId: number
  trackingStatus: string | null
}>()

const statusOptions = [
  'not shipped',
  'gate out empty',
  'gate in',
  'loaded on vessel',
  'vessel departed',
  'in transit',
  'vessel arrived',
  'discharged',
  'delivered',
  'gate out for delivery',
  'empty container return',
  'completed',
  'on hold',
  'cancelled',
]

const form = useForm({
  tracking_status: props.trackingStatus ?? 'not shipped',
  status_date: '',
  location: '',
  country: '',
  remarks: '',
})

function submit(): void {
  form.patch(updateTrackingStatus.url(props.shipmentId), {
    preserveScroll: true,
  })
}
</script>

<template>
  <form class="space-y-4 rounded-lg border p-5" @submit.prevent="submit">
    <div>
      <h3 class="font-semibold">Update tracking status</h3>
      <p class="text-sm text-muted-foreground">Each update is added to the shipment timeline.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <label class="space-y-2 text-sm font-medium">
        Status
        <select v-model="form.tracking_status" class="h-10 w-full rounded-md border border-input bg-background px-3">
          <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
        </select>
        <span v-if="form.errors.tracking_status" class="block text-xs text-destructive">{{ form.errors.tracking_status }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        Status date
        <Input v-model="form.status_date" type="date" />
        <span v-if="form.errors.status_date" class="block text-xs text-destructive">{{ form.errors.status_date }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        Location
        <Input v-model="form.location" placeholder="Port, terminal, or warehouse" />
      </label>
      <label class="space-y-2 text-sm font-medium">
        Country
        <Input v-model="form.country" />
      </label>
      <label class="space-y-2 text-sm font-medium sm:col-span-2">
        Remarks
        <Textarea v-model="form.remarks" rows="2" />
      </label>
    </div>

    <Button type="submit" :disabled="form.processing">
      {{ form.processing ? 'Saving...' : 'Save tracking update' }}
    </Button>
  </form>
</template>