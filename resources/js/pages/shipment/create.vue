<script setup lang="ts">
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next'
import { index, store as createShipment } from '@/actions/App/Http/Controllers/ShipmentController'

import { Button } from '@/components/ui/button'
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
  Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue,
} from '@/components/ui/select'
import {
  Tabs, TabsContent, TabsList, TabsTrigger,
} from '@/components/ui/tabs'
import shipmentDocuments from '@/routes/shipment-documents'
import ShipmentStatusHistory from '@/components/shipment/ShipmentStatusHistory.vue'


interface Option {
  id: number
  [key: string]: string | number
}
interface PurchaseOrder {
  id: number
  supplier_id: number
  supplier: Option | null
  po_number: string
}
interface PurchaseOrderItem {
  id: number
  purchase_order_id: number
  product_code: string
  product_name: string
  quantity: number
  remaining_qty?: number
  backorder_qty: number
  unit: string
  hs_code: string
  facility: string
  import_duty: number
  total_price: number
  po_number?: string | null
}
interface ContainerItem {
  purchase_order_id: number | null
  purchase_order_item_id: number | null
  quantity: number | null
}
interface ShipmentContainer {
  container_number: string
  container_seal_number: string
  container_size?: string
  items: ContainerItem[]
}
interface ShipmentMilestone {
  milestone_name: string
  status: 'Planned' | 'In Progress' | 'Completed'
  planned_date: string
  actual_date: string
  remarks: string
}

interface shipmentDocuments {
  document_name: string
  document_type: string
  document_file: string
  document_date: Date
}


const props = defineProps<{
  shipment_number: string
  purchaseOrders?: PurchaseOrder[]
  items?: PurchaseOrderItem[]
  forwarders?: Option[]
  shippingLines?: Option[]
  ports?: Option[]
  warehouses?: Option[]
  documentTypes?: Record<string, string>
  requiredDocumentTypes?: string[]
}>()

const availablePurchaseOrders = computed(() =>
  purchaseOrders.value.filter(
    (purchaseOrder: PurchaseOrder) => getitems(purchaseOrder.id).length > 0,
  ),
)

const getitems = (purchaseOrderId: number | null) => {
  if (!purchaseOrderId) {
    return []
  }

  return poItems.value.filter(
    (item) =>
      Number(item.purchase_order_id) === Number(purchaseOrderId) &&
      getRemainingQuantity(item) > 0,
  )
}
const getRemainingQuantity = (item: PurchaseOrderItem) =>
    Math.max(0, Number(item.remaining_qty ?? item.quantity ?? 0));

const createEmptyContainer = (): ShipmentContainer => ({
  container_number: '',
  container_seal_number: '',
  container_size: '',
  items: [],
})
const statusDocument = computed(() => {
  const total = props.requiredDocumentTypes?.length ?? 0

  return total > 0
    ? `Missing (0/${total} documents uploaded)`
    : 'No required documents'
})
const statusHistories = () => {
   if (ShipmentStatusHistory.length > 0) {
    return ShipmentStatusHistory[0].status
  }
}

const form = useForm({
  shipment_number: props.shipment_number,
  forwarder_id: '',
  shipping_line_id: '',
  purchase_order_ids: [] as number[],
  port_of_loading_id: '',
  port_of_discharge_id: '',
  warehouse_id: '',
  bl_number: '',
  invoice_number: '',
  vessel_name: '',
  voyage_number: '',
  etd: '',
  eta: '',
  atd: '',
  ata: '',
  milestones: [] as ShipmentMilestone[],
  remarks: '',
  containers: [createEmptyContainer()] as ShipmentContainer[],
})

const addContainerItem = (containerIndex: number) => {
    form.containers[containerIndex].items.push({
        purchase_order_id: null,
        purchase_order_item_id: null,
        quantity: null,
    })
}

const addMilestone = () => form.milestones.push({
  milestone_name: '',
  status: 'Planned',
  planned_date: '',
  actual_date: '',
  remarks: '',
})

const trackingStatus = computed(() => 'not shipped')

const purchaseOrders = computed(() => props.purchaseOrders ?? [])
const poItems = computed(() => props.items ?? [])
const forwarderOptions = computed(() => props.forwarders ?? [])
const shippingLineOptions = computed(() => props.shippingLines ?? [])
const portOptions = computed(() => props.ports ?? [])
const warehouseOptions = computed(() => props.warehouses ?? [])

const selectedPos = computed(() => {
  const selectedIds = new Set(
    form.containers.flatMap((container) => container.items.flatMap((row) => {
      if (row.purchase_order_id !== null) return [row.purchase_order_id]
      const item = poItems.value.find((poItem) => poItem.id === row.purchase_order_item_id)
      return item ? [item.purchase_order_id] : []
    })),
  )

  return purchaseOrders.value.filter((po) => selectedIds.has(po.id))
})

const hasOverAllocation = computed(() => {
  const allocated = new Map<number, number>()
  form.containers.forEach((container) => container.items.forEach((row) => {
    if (row.purchase_order_item_id === null) return
    allocated.set(row.purchase_order_item_id, (allocated.get(row.purchase_order_item_id) ?? 0) + Number(row.quantity || 0))
  }))

  return poItems.value.some((item) => (allocated.get(item.id) ?? 0) > item.backorder_qty)
})

const syncHeaderPurchaseOrders = () => {
  form.purchase_order_ids = selectedPos.value.map((po) => po.id)
}

const addContainer = () => {
  form.containers.push(createEmptyContainer())
}

const removeContainer = (containerIndex: number) => {
  if (form.containers.length > 1) form.containers.splice(containerIndex, 1)
}


const removeContainerItem = (containerIndex: number, itemIndex: number) => {
  form.containers[containerIndex]?.items.splice(itemIndex, 1)
}

const submit = () => {
  syncHeaderPurchaseOrders()
  if (form.containers.some((container) => container.items.some((row) =>
    row.purchase_order_item_id === null || Number(row.quantity) <= 0 || !Number.isFinite(Number(row.quantity))
  ))) return
  form.post(createShipment.url(), { preserveScroll: true })
}
</script>

<template>
  <main class="mx-4 w-full space-y-6 rounded-lg border bg-card p-6 sm:p-8">
    <div>
      <Link :href="index.url()" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
        <ArrowLeft class="h-4 w-4" /> Back to Shipments
      </Link>
      <h1 class="mt-4 text-2xl font-semibold tracking-tight">Add Shipment</h1>
      <p class="text-sm text-muted-foreground">Create a shipment and allocate quantities to containers.</p>
    </div>

    <form class="space-y-6" @submit.prevent="submit">
      <section class="rounded-lg border bg-card">
        <div class="border-b p-5"><h2 class="font-semibold">Shipment Information</h2></div>
        <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
          <div class="space-y-2">
            <label class="text-sm font-medium">Shipment Number</label>
            <Input v-model="form.shipment_number" readonly />
            <p v-if="form.errors.shipment_number" class="text-sm text-destructive">{{ form.errors.shipment_number }}</p>
          </div>
          <div class="space-y-2"><label class="text-sm font-medium">Invoice Number</label><Input v-model="form.invoice_number" /></div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Purchase Orders in Containers</label>
            <div class="flex min-h-10 flex-wrap items-center gap-2 rounded-md border bg-muted/30 p-2">
              <span v-if="selectedPos.length === 0" class="text-sm text-muted-foreground">POs appear automatically when items are allocated.</span>
              <span v-for="po in selectedPos" :key="po.id" class="rounded-md border bg-background px-2 py-1 text-xs font-medium">{{ po.po_number }}</span>
            </div>
            <p class="text-xs text-muted-foreground">Automatically aggregated from every container; one shipment can contain multiple POs.</p>
            <p v-if="form.errors.purchase_order_ids" class="text-sm text-destructive">{{ form.errors.purchase_order_ids }}</p>
          </div>
          <div class="space-y-2"><label class="text-sm font-medium">BL Number</label><Input v-model="form.bl_number" /><p v-if="form.errors.bl_number" class="text-sm text-destructive">{{ form.errors.bl_number }}</p></div>
          <div class="space-y-2"><label class="text-sm font-medium">Vessel Name</label><Input v-model="form.vessel_name" /></div>
          <div class="space-y-2"><label class="text-sm font-medium">Voyage Number</label><Input v-model="form.voyage_number" /></div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Status Document</label>

            <Input
              :model-value="statusDocument"
              disabled
              class="bg-muted"
            />
            <p class="text-xs text-muted-foreground">
              Status akan diperbarui setelah dokumen diunggah dan diverifikasi.
            </p>
          </div>
            
            <!-- <Select v-model="form.status_document">
              <SelectTrigger class="w-full"><SelectValue placeholder="Select status" /></SelectTrigger>
              <SelectContent><SelectGroup><SelectLabel>Status Document</SelectLabel><SelectItem v-for="status in statusOptions" :key="status" :value="status">{{ status }}</SelectItem></SelectGroup></SelectContent>
            </Select> -->

          <div class="space-y-2">
            <label class="text-sm font-medium">Tracking Status</label>

            <Input
              :model-value="trackingStatus"
              disabled
              class="bg-muted"
            />
            <p class="text-xs text-muted-foreground">
              Tracking status akan diperbarui berdasarkan aktivitas perjalanan shipment.
            </p>
          </div>
            <!-- <Select v-model="form.tracking_status">
              <SelectTrigger class="w-full"><SelectValue placeholder="Select tracking status" /></SelectTrigger>
              <SelectContent><SelectGroup><SelectLabel>Tracking Status</SelectLabel><SelectItem v-for="status in trackingStatusOptions" :key="status" :value="status">{{ status }}</SelectItem></SelectGroup></SelectContent>
            </Select> -->
        </div>
      </section>

      <section class="rounded-lg border bg-card p-5">
        <Tabs default-value="containers" class="w-full">
          <TabsList class="flex h-auto flex-wrap">
            <TabsTrigger value="containers">Containers & Items</TabsTrigger>
            <TabsTrigger value="schedule">Shipment Schedule</TabsTrigger>
            <TabsTrigger value="related">Related Information</TabsTrigger>
          </TabsList>

          <TabsContent value="containers" class="space-y-5 pt-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div><h2 class="font-semibold">Container Allocation</h2><p class="text-sm text-muted-foreground">Select an existing PO line and enter the quantity shipped. PO item details and total price remain unchanged.</p></div>
              <Button type="button" variant="outline" @click="addContainer"><Plus class="mr-2 h-4 w-4" /> Add Container</Button>
            </div>

            <div v-for="(container, containerIndex) in form.containers" :key="containerIndex" class="space-y-5 rounded-xl border p-4">
              <div class="flex items-center justify-between">
                <h3 class="font-semibold">Container {{ containerIndex + 1 }}</h3>
                <Button type="button" variant="ghost" size="icon" :disabled="form.containers.length <= 1" aria-label="Remove container" @click="removeContainer(containerIndex)"><Trash2 class="h-4 w-4 text-destructive" /></Button>
              </div>
              <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2"><label class="text-sm font-medium">Container Number</label><Input v-model="container.container_number" placeholder="e.g. MSCU1234567" /><p v-if="form.errors[`containers.${containerIndex}.container_number`]" class="text-xs text-destructive">{{ form.errors[`containers.${containerIndex}.container_number`] }}</p></div>
                <div class="space-y-2"><label class="text-sm font-medium">Container Seal Number</label><Input v-model="container.container_seal_number" placeholder="Enter seal number" /><p v-if="form.errors[`containers.${containerIndex}.container_seal_number`]" class="text-xs text-destructive">{{ form.errors[`containers.${containerIndex}.container_seal_number`] }}</p></div>
                <div class="space-y-2"><label class="text-sm font-medium">Container Size</label><Input v-model="container.container_size" placeholder="e.g. 20ft, 40ft" /><p v-if="form.errors[`containers.${containerIndex}.container_size`]" class="text-xs text-destructive">{{ form.errors[`containers.${containerIndex}.container_size`] }}</p></div>
              </div>

              <div class="space-y-4 border-t pt-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <h4 class="font-medium">PO Line Items</h4>
                    <p class="text-sm text-muted-foreground">Each item is one table row. Add items to append rows to this table.</p>
                  </div>
                  <Button type="button" variant="outline" @click="addContainerItem(containerIndex)">
                    <Plus class="mr-2 h-4 w-4" /> Add PO Item
                  </Button>
                </div>

                <div class="w-full overflow-x-auto rounded-md border">
                  <Table class="w-full min-w-[1150px]">
                    <TableHeader>
                      <TableRow>
                        <TableHead class="w-12">No.</TableHead>
                        <TableHead class="min-w-[300px]">Purchase Order</TableHead>
                        <TableHead class="min-w-[300px]">Purchase Order Item</TableHead>
                        <TableHead class="w-[180px]">Quantity</TableHead>
                        <TableHead class="min-w-[360px]">Unit</TableHead>
                        <TableHead class="w-[72px] text-right">Action</TableHead>
                      </TableRow>
                    </TableHeader>

                    <TableBody>
                      <TableRow v-for="(row, itemIndex) in container.items" :key="itemIndex">
                        <TableCell class="align-middle text-muted-foreground">
                          {{ itemIndex + 1 }}
                        </TableCell>

                        <TableCell class="align-middle">
                          <select
                              v-model.number="row.purchase_order_id"
                              class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                              @change="
                                  row.purchase_order_item_id = null;
                                  row.quantity = null;
                                "
                          >
                            <option value="" disabled>
                              {{ availablePurchaseOrders.length ? 'Select Purchase Order' : 'No import PO available' }}
                            </option>
                            <option v-for="purchaseOrder in availablePurchaseOrders" :value="purchaseOrder.id" :key="purchaseOrder.id">
                              {{ purchaseOrder.supplier?.supplier_name }} - {{ purchaseOrder.po_number }}
                            </option>
                          </select>
                        </TableCell>

                        <TableCell class="align-middle">
                          <select
                            v-model.number="row.purchase_order_item_id"
                            class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!row.purchase_order_id"
                            @change="row.quantity = null"
                          >
                            <option value="" disabled>Select PO item</option>
                            <option
                              v-for="item in getitems(row.purchase_order_id)"
                              :key="item.id"
                              :value="item.id"
                            >
                              {{ item.product_code }} — {{ item.product_name }}
                              ({{ getRemainingQuantity(item) }} {{ item.unit }} available)
                            </option>
                          </select>
                        </TableCell>

                        <TableCell class="align-middle">
                          <Input
                            :model-value="row.quantity ?? ''"
                            @update:model-value="row.quantity = $event === '' ? null : Number($event)"
                            type="number"
                            min="0.01"
                            step="0.01"
                            :disabled="!row.purchase_order_item_id"
                            placeholder="0"
                            class="min-w-[120px]"
                          />
                        </TableCell>
                        <TableCell class="align-middle text-sm text-muted-foreground">
                          {{ poItems.find((item) => item.id === row.purchase_order_item_id)?.unit ?? '-' }}
                        </TableCell>
                        <TableCell class="text-right align-middle">
                          <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            aria-label="Remove PO item"
                            @click="removeContainerItem(containerIndex, itemIndex)"
                          >
                            <Trash2 class="h-4 w-4 text-destructive" />
                          </Button>
                        </TableCell>
                      </TableRow>

                      <TableRow v-if="container.items.length === 0">
                        <TableCell :colspan="6" class="h-24 text-center text-muted-foreground">
                          No PO items assigned to this container. Click “Add PO Item” to add a row.
                        </TableCell>
                      </TableRow>
                    </TableBody>
                  </Table>
                </div>
                <p class="text-xs text-muted-foreground">
                  {{ container.items.length }} item row(s)
                </p>
              </div>
            </div>
            <div class="flex flex-wrap justify-between gap-3 rounded-lg bg-muted/30 p-4">
              <div class="text-sm"><span class="text-muted-foreground">POs included:</span> <strong>{{ selectedPos.length }}</strong></div>
            </div>
            <p class="text-xs text-muted-foreground">The PO line's quantity, product details, and total_price are not edited here. On save, the server must deduct the summed container quantity from that line's backorder_qty inside a database transaction.</p>
          </TabsContent>
          <TabsContent value="schedule" class="pt-5">
            <h2 class="mb-4 font-semibold">Shipment Schedule</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <div class="space-y-2"><label class="text-sm font-medium">ETD</label><Input v-model="form.etd" type="date" /></div>
              <div class="space-y-2"><label class="text-sm font-medium">ETA</label><Input v-model="form.eta" type="date" /></div>
              <div class="space-y-2"><label class="text-sm font-medium">Actual Departure (ATD)</label><Input v-model="form.atd" type="date" /></div>
              <div class="space-y-2"><label class="text-sm font-medium">Actual Arrival (ATA)</label><Input v-model="form.ata" type="date" /></div>
            </div>
            <div class="mt-8 space-y-4">
              <div class="flex items-center justify-between gap-3">
                <div><h3 class="font-semibold">Milestone</h3><p class="text-sm text-muted-foreground">Catat rencana dan realisasi tahapan shipment.</p></div>
                <Button type="button" variant="outline" @click="addMilestone"><Plus class="mr-2 h-4 w-4" /> Tambah milestone</Button>
              </div>
              <div v-for="(milestone, index) in form.milestones" :key="index" class="grid gap-3 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-5">
                <Input v-model="milestone.milestone_name" placeholder="Nama milestone" aria-label="Nama milestone" />
                <select v-model="milestone.status" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                  <option value="Planned">Planned</option><option value="In Progress">In Progress</option><option value="Completed">Completed</option>
                </select>
                <Input v-model="milestone.planned_date" type="date" aria-label="Tanggal rencana" />
                <Input v-model="milestone.actual_date" type="date" aria-label="Tanggal aktual" />
                <div class="flex gap-2"><Input v-model="milestone.remarks" placeholder="Catatan" aria-label="Catatan milestone" /><Button type="button" variant="ghost" size="icon" aria-label="Hapus milestone" @click="form.milestones.splice(index, 1)"><Trash2 class="h-4 w-4 text-destructive" /></Button></div>
              </div>
            </div>
          </TabsContent>

          <TabsContent value="related" class="pt-5">
            <h2 class="mb-4 font-semibold">Related Information</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <div class="space-y-2"><label class="text-sm font-medium">Forwarder</label><Select v-model="form.forwarder_id"><SelectTrigger class="w-full"><SelectValue placeholder="Select forwarder" /></SelectTrigger><SelectContent><SelectGroup><SelectLabel>Forwarder</SelectLabel><SelectItem v-for="item in forwarderOptions" :key="item.id" :value="String(item.id)">{{ item.forwarder_name }}</SelectItem></SelectGroup></SelectContent></Select></div>
              <div class="space-y-2"><label class="text-sm font-medium">Shipping Line</label><Select v-model="form.shipping_line_id"><SelectTrigger class="w-full"><SelectValue placeholder="Select shipping line" /></SelectTrigger><SelectContent><SelectGroup><SelectLabel>Shipping Line</SelectLabel><SelectItem v-for="item in shippingLineOptions" :key="item.id" :value="String(item.id)">{{ item.shipping_line_name }}</SelectItem></SelectGroup></SelectContent></Select></div>
              <div class="space-y-2"><label class="text-sm font-medium">Port of Loading</label><Select v-model="form.port_of_loading_id"><SelectTrigger class="w-full"><SelectValue placeholder="Select port" /></SelectTrigger><SelectContent><SelectGroup><SelectLabel>Port of Loading</SelectLabel><SelectItem v-for="item in portOptions" :key="item.id" :value="String(item.id)">{{ item.port_name }}</SelectItem></SelectGroup></SelectContent></Select></div>
              <div class="space-y-2"><label class="text-sm font-medium">Port of Discharge</label><Select v-model="form.port_of_discharge_id"><SelectTrigger class="w-full"><SelectValue placeholder="Select port" /></SelectTrigger><SelectContent><SelectGroup><SelectLabel>Port of Discharge</SelectLabel><SelectItem v-for="item in portOptions" :key="item.id" :value="String(item.id)">{{ item.port_name }}</SelectItem></SelectGroup></SelectContent></Select></div>
              <div class="space-y-2"><label class="text-sm font-medium">Warehouse</label><Select v-model="form.warehouse_id"><SelectTrigger class="w-full"><SelectValue placeholder="Select warehouse" /></SelectTrigger><SelectContent><SelectGroup><SelectLabel>Warehouse</SelectLabel><SelectItem v-for="item in warehouseOptions" :key="item.id" :value="String(item.id)">{{ item.warehouse_name }}</SelectItem></SelectGroup></SelectContent></Select></div>
              <div class="space-y-2 sm:col-span-2 lg:col-span-3"><label class="text-sm font-medium">Remarks</label><Textarea v-model="form.remarks" /></div>
            </div>
          </TabsContent>
        </Tabs>
      </section>

      <div class="flex flex-wrap justify-end gap-3">
        <Button type="button" variant="outline" as-child><Link href="/shipments">Cancel</Link></Button>
        <Button type="submit" :disabled="form.processing || hasOverAllocation || form.containers.some(container => container.items.some(row => row.purchase_order_item_id === null || Number(row.quantity) <= 0 || !Number.isFinite(Number(row.quantity))))">
          {{ form.processing ? 'Saving...' : 'Save Shipment' }}
        </Button>
      </div>
    </form>
  </main>
</template>
