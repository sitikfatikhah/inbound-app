<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { store as storeScheduleRevision } from '@/actions/App/Http/Controllers/ShipmentScheduleHistoriesController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps<{
  shipmentId: number
  eta: string | null
  etd: string | null
}>()

const form = useForm({
  eta: props.eta?.slice(0, 10) ?? '',
  etd: props.etd?.slice(0, 10) ?? '',
  revision_reason: '',
})

const hasScheduleChange = computed(
  () => form.eta !== (props.eta?.slice(0, 10) ?? '') || form.etd !== (props.etd?.slice(0, 10) ?? ''),
)

function submit(): void {
  form.post(storeScheduleRevision.url(props.shipmentId), {
    preserveScroll: true,
    onSuccess: () => form.reset('revision_reason'),
  })
}
</script>

<template>
  <form class="space-y-4 rounded-lg border p-5" @submit.prevent="submit">
    <div>
      <h3 class="font-semibold">Revise shipment schedule</h3>
      <p class="text-sm text-muted-foreground">Setiap perubahan ETA/ETD wajib disertai alasan dan tersimpan sebagai histori.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <label class="space-y-2 text-sm font-medium">
        ETA
        <Input v-model="form.eta" type="date" />
        <span v-if="form.errors.eta" class="block text-xs text-destructive">{{ form.errors.eta }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        ETD
        <Input v-model="form.etd" type="date" />
        <span v-if="form.errors.etd" class="block text-xs text-destructive">{{ form.errors.etd }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium sm:col-span-2">
        Revision reason
        <Textarea v-model="form.revision_reason" rows="2" required />
        <span v-if="form.errors.revision_reason" class="block text-xs text-destructive">{{ form.errors.revision_reason }}</span>
      </label>
    </div>

    <Button type="submit" :disabled="form.processing || !hasScheduleChange || !form.revision_reason.trim()">
      {{ form.processing ? 'Saving...' : 'Save schedule revision' }}
    </Button>
  </form>
</template>