<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { update as updateCustomsClearance } from '@/actions/App/Http/Controllers/ShipmentCustomsClearancesController'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

interface CustomsClearance {
  jalur: 'Hijau' | 'Merah' | null
  pib_number: string | null
  pib_date: string | null
  inspection_start: string | null
  inspection_end: string | null
  sppb_number: string | null
  sppb_date: string | null
  remarks: string | null
}

const props = defineProps<{
  shipmentId: number
  clearance: CustomsClearance | null
}>()

function dateTimeInput(value: string | null): string {
  return value ? value.replace(' ', 'T').slice(0, 16) : ''
}

const form = useForm({
  jalur: props.clearance?.jalur ?? 'Hijau',
  pib_number: props.clearance?.pib_number ?? '',
  pib_date: props.clearance?.pib_date?.slice(0, 10) ?? '',
  inspection_start: dateTimeInput(props.clearance?.inspection_start ?? null),
  inspection_end: dateTimeInput(props.clearance?.inspection_end ?? null),
  sppb_number: props.clearance?.sppb_number ?? '',
  sppb_date: props.clearance?.sppb_date?.slice(0, 10) ?? '',
  remarks: props.clearance?.remarks ?? '',
})

function submit(): void {
  form.put(updateCustomsClearance.url(props.shipmentId), {
    preserveScroll: true,
  })
}
</script>

<template>
  <form class="space-y-4 rounded-lg border p-5" @submit.prevent="submit">
    <div>
      <h3 class="font-semibold">Update customs clearance</h3>
      <p class="text-sm text-muted-foreground">Catat pengajuan PIB, hasil jalur CEISA, pemeriksaan, dan penerbitan SPPB.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <label class="space-y-2 text-sm font-medium">
        PIB number
        <Input v-model="form.pib_number" />
        <span v-if="form.errors.pib_number" class="block text-xs text-destructive">{{ form.errors.pib_number }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        PIB submission date
        <Input v-model="form.pib_date" type="date" />
        <span v-if="form.errors.pib_date" class="block text-xs text-destructive">{{ form.errors.pib_date }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        CEISA channel
        <select v-model="form.jalur" class="h-10 w-full rounded-md border border-input bg-background px-3">
          <option value="Hijau">Hijau</option>
          <option value="Merah">Merah</option>
        </select>
        <span v-if="form.errors.jalur" class="block text-xs text-destructive">{{ form.errors.jalur }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        SPPB number
        <Input v-model="form.sppb_number" />
        <span v-if="form.errors.sppb_number" class="block text-xs text-destructive">{{ form.errors.sppb_number }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        Inspection started
        <Input v-model="form.inspection_start" type="datetime-local" />
        <span v-if="form.errors.inspection_start" class="block text-xs text-destructive">{{ form.errors.inspection_start }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        Inspection completed
        <Input v-model="form.inspection_end" type="datetime-local" />
        <span v-if="form.errors.inspection_end" class="block text-xs text-destructive">{{ form.errors.inspection_end }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium">
        SPPB date
        <Input v-model="form.sppb_date" type="date" />
        <span v-if="form.errors.sppb_date" class="block text-xs text-destructive">{{ form.errors.sppb_date }}</span>
      </label>
      <label class="space-y-2 text-sm font-medium sm:col-span-2">
        Remarks
        <Textarea v-model="form.remarks" rows="2" />
      </label>
    </div>

    <Button type="submit" :disabled="form.processing">
      {{ form.processing ? 'Saving...' : 'Save customs status' }}
    </Button>
  </form>
</template>