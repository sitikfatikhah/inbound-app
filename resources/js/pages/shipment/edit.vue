<script setup lang="ts">
import { computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next'
import { update as updateShipment } from '@/actions/App/Http/Controllers/ShipmentController'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

interface Option {
  id: number
  [key: string]: string | number
}

interface PurchaseOrder {
  id: number
  po_number: string
}

interface PurchaseOrderItem {
  id: number
  purchase_order_id: number
  product_code: string
  product_name: string
  quantity: number
  remaining_qty: number
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
  id?: number
  container_number: string
  container_seal_number: string
  items: ContainerItem[]
}

interface ShipmentMilestone {
  milestone_name: string
  status: 'Planned' | 'In Progress' | 'Completed'
  planned_date: string
  actual_date: string
  remarks: string
}

interface Shipment {
  id: number
  shipment_number: string
  forwarder_id: number | null
  shipping_line_id: number | null
  port_of_loading_id: number | null
  port_of_discharge_id: number | null
  warehouse_id: number | null
  bl_number: string | null
  invoice_number: string | null
  vessel_name: string | null
  voyage_number: string | null
  etd: string | null
  eta: string | null
  atd: string | null
  ata: string | null
  status_document: string | null
  tracking_status: string | null
  remarks: string | null
}

const props = defineProps<{
  shipment: Shipment
  containers: ShipmentContainer[]
  milestones?: ShipmentMilestone[]
  purchaseOrders?: PurchaseOrder[]
  purchaseOrderItems?: PurchaseOrderItem[]
  forwarders?: Option[]
  shippingLines?: Option[]
  ports?: Option[]
  warehouses?: Option[]
}>()

const purchaseOrders = computed(() => props.purchaseOrders ?? [])
const poItems = computed(() => props.purchaseOrderItems ?? [])
const forwarderOptions = computed(() => props.forwarders ?? [])
const shippingLineOptions = computed(() => props.shippingLines ?? [])
const portOptions = computed(() => props.ports ?? [])
const warehouseOptions = computed(() => props.warehouses ?? [])

const createEmptyContainer = (): ShipmentContainer => ({
  container_number: '',
  container_seal_number: '',
  items: [],
})

const createInitialContainers = (): ShipmentContainer[] => {
  if (!props.containers?.length) {
    return [createEmptyContainer()]
  }

  return props.containers.map((container) => ({
    id: container.id,
    container_number: container.container_number ?? '',
    container_seal_number: container.container_seal_number ?? '',
    items: (container.items ?? []).map((row) => {
      const poItem = poItems.value.find(
        (item) =>
          Number(item.id) === Number(row.purchase_order_item_id),
      )

      return {
        purchase_order_id:
          row.purchase_order_id ?? poItem?.purchase_order_id ?? null,
        purchase_order_item_id: row.purchase_order_item_id ?? null,
        quantity: row.quantity ?? null,
      }
    }),
  }))
}

const form = useForm({
  shipment_number: props.shipment.shipment_number,
  forwarder_id:
    props.shipment.forwarder_id == null
      ? ''
      : String(props.shipment.forwarder_id),
  shipping_line_id:
    props.shipment.shipping_line_id == null
      ? ''
      : String(props.shipment.shipping_line_id),
  purchase_order_ids: [] as number[],
  port_of_loading_id:
    props.shipment.port_of_loading_id == null
      ? ''
      : String(props.shipment.port_of_loading_id),
  port_of_discharge_id:
    props.shipment.port_of_discharge_id == null
      ? ''
      : String(props.shipment.port_of_discharge_id),
  warehouse_id:
    props.shipment.warehouse_id == null
      ? ''
      : String(props.shipment.warehouse_id),
  bl_number: props.shipment.bl_number ?? '',
  invoice_number: props.shipment.invoice_number ?? '',
  vessel_name: props.shipment.vessel_name ?? '',
  voyage_number: props.shipment.voyage_number ?? '',
  etd: props.shipment.etd ?? '',
  eta: props.shipment.eta ?? '',
  atd: props.shipment.atd ?? '',
  ata: props.shipment.ata ?? '',
  milestones: (props.milestones ?? []).map((milestone) => ({ ...milestone })),
  status_document:
    props.shipment.status_document ?? 'purchase order created',
  tracking_status:
    props.shipment.tracking_status ?? 'not shipped',
  remarks: props.shipment.remarks ?? '',
  containers: createInitialContainers(),
})

const statusOptions = [
  'PL & INV created',
  'BL Draft',
  'BL Final',
  'LCC created',
  'PTDI',
  'sent document',
  'draft PIB',
  'Billing Bea Cukai',
  'BPN',
  'customs clearance',
  'Green Lane - SPPB',
  'Red Lane - SPJM',
  'NPD',
  'NHI',
  'SPTNP',
  'SPKTNP',
  'SPBL',
  'Gate Out',
  'Others',
]

const trackingStatusOptions = [
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

const getPoItem = (id: number | null) => {
  if (id === null) return undefined

  return poItems.value.find(
    (item) => Number(item.id) === Number(id),
  )
}

/*
 * Quantities already allocated to this shipment when the edit
 * page opened. These are added back to remaining_qty so the
 * current shipment does not count against itself twice.
 *
 * This assumes remaining_qty excludes this shipment's existing
 * allocation. Adjust the calculation if the backend uses a
 * different definition.
 */
const originalShipmentAllocation = (poItemId: number) =>
  props.containers.reduce(
    (total, container) =>
      total +
      (container.items ?? []).reduce((subtotal, row) => {
        if (
          Number(row.purchase_order_item_id) !== Number(poItemId)
        ) {
          return subtotal
        }

        return subtotal + Math.max(0, Number(row.quantity) || 0)
      }, 0),
    0,
  )

const availableForShipment = (item: PurchaseOrderItem) =>
  Math.max(
    0,
    Number(item.remaining_qty || 0) +
      originalShipmentAllocation(item.id),
  )

const allocatedQty = (
  poItemId: number,
  exceptContainer = -1,
  exceptItem = -1,
) =>
  form.containers.reduce(
    (total, container, containerIndex) =>
      total +
      container.items.reduce((subtotal, row, itemIndex) => {
        if (
          containerIndex === exceptContainer &&
          itemIndex === exceptItem
        ) {
          return subtotal
        }

        if (
          Number(row.purchase_order_item_id) !== Number(poItemId)
        ) {
          return subtotal
        }

        return subtotal + Math.max(0, Number(row.quantity) || 0)
      }, 0),
    0,
  )

const remainingQty = (
  item: PurchaseOrderItem,
  containerIndex = -1,
  itemIndex = -1,
) =>
  Math.max(
    0,
    availableForShipment(item) -
      allocatedQty(item.id, containerIndex, itemIndex),
  )

const poItemsForOrder = (poId: number | null) => {
  if (poId === null) return []

  return poItems.value.filter(
    (item) => Number(item.purchase_order_id) === Number(poId),
  )
}

const rowPoItem = (row: ContainerItem) =>
  getPoItem(row.purchase_order_item_id)

const formatNumber = (value: number) =>
  Number(value || 0).toLocaleString('id-ID', {
    maximumFractionDigits: 2,
  })

const selectedPos = computed(() => {
  const ids = new Set<number>()

  form.containers.forEach((container) => {
    container.items.forEach((row) => {
      const item = getPoItem(row.purchase_order_item_id)

      if (item && Number(row.quantity) > 0) {
        ids.add(Number(item.purchase_order_id))
      }
    })
  })

  return purchaseOrders.value.filter(
    (po) => ids.has(Number(po.id)),
  )
})

const syncHeaderPurchaseOrders = () => {
  form.purchase_order_ids = [
    ...new Set(
      form.containers.flatMap((container) =>
        container.items.flatMap((row) => {
          const item = getPoItem(row.purchase_order_item_id)

          return item ? [Number(item.purchase_order_id)] : []
        }),
      ),
    ),
  ]
}

watch(
  () => form.containers,
  syncHeaderPurchaseOrders,
  { deep: true, immediate: true },
)

const addContainer = () => {
  form.containers.push(createEmptyContainer())
}

const removeContainer = (containerIndex: number) => {
  if (form.containers.length <= 1) return

  form.containers.splice(containerIndex, 1)
}

const addContainerItem = (containerIndex: number) => {
  const container = form.containers[containerIndex]

  if (!container) return

  // Adds one row only; does not create another container/card.
  container.items.push({
    purchase_order_id: null,
    purchase_order_item_id: null,
    quantity: null,
  })
}

const removeContainerItem = (
  containerIndex: number,
  itemIndex: number,
) => {
  form.containers[containerIndex]?.items.splice(itemIndex, 1)
}

const addMilestone = () => form.milestones.push({
  milestone_name: '',
  status: 'Planned',
  planned_date: '',
  actual_date: '',
  remarks: '',
})

const onPurchaseOrderChange = (
  row: ContainerItem,
  value: string,
) => {
  const id = value === '' ? null : Number(value)

  row.purchase_order_id =
    id !== null && Number.isFinite(id) ? id : null

  row.purchase_order_item_id = null
  row.quantity = null
}

const onPoItemChange = (
  row: ContainerItem,
  value: string,
) => {
  const id = value === '' ? null : Number(value)
  const item =
    id !== null && Number.isFinite(id)
      ? getPoItem(id)
      : undefined

  row.purchase_order_item_id = item?.id ?? null
  row.purchase_order_id = item?.purchase_order_id ?? null
  row.quantity = null
}

const hasOverAllocation = computed(() =>
  form.containers.some((container, containerIndex) =>
    container.items.some((row, itemIndex) => {
      const item = getPoItem(row.purchase_order_item_id)

      if (!item || row.quantity === null) return false

      return (
        Number(row.quantity) >
        remainingQty(item, containerIndex, itemIndex)
      )
    }),
  ),
)

const hasInvalidItems = computed(() =>
  form.containers.some((container) =>
    container.items.some((row) => {
      const item = getPoItem(row.purchase_order_item_id)

      return (
        row.purchase_order_id === null ||
        row.purchase_order_item_id === null ||
        !item ||
        Number(item.purchase_order_id) !==
          Number(row.purchase_order_id) ||
        row.quantity === null ||
        !Number.isFinite(Number(row.quantity)) ||
        Number(row.quantity) <= 0
      )
    }),
  ),
)

const submit = () => {
  syncHeaderPurchaseOrders()

  if (hasInvalidItems.value || hasOverAllocation.value) {
    return
  }

  form
    .transform((data) => ({
      ...data,
      forwarder_id: data.forwarder_id
        ? Number(data.forwarder_id)
        : null,
      shipping_line_id: data.shipping_line_id
        ? Number(data.shipping_line_id)
        : null,
      port_of_loading_id: data.port_of_loading_id
        ? Number(data.port_of_loading_id)
        : null,
      port_of_discharge_id: data.port_of_discharge_id
        ? Number(data.port_of_discharge_id)
        : null,
      warehouse_id: data.warehouse_id
        ? Number(data.warehouse_id)
        : null,
      containers: data.containers.map((container) => ({
        container_number: container.container_number,
        container_seal_number: container.container_seal_number,
        items: container.items.map((row) => {
          const item = getPoItem(row.purchase_order_item_id)

          return {
            purchase_order_id: Number(item?.purchase_order_id),
            purchase_order_item_id: Number(
              row.purchase_order_item_id,
            ),
            quantity: Number(row.quantity),
          }
        }),
      })),
    }))
    .put(updateShipment.url(props.shipment.id), {
      preserveScroll: true,
    })
}
</script>

<template>
  <div class="mx-4 w-full space-y-6 rounded-lg border bg-card p-6 sm:mx-0 sm:p-8">
    <div>
      <Link
        href="/shipments"
        class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft class="h-4 w-4" />
        Back to Shipments
      </Link>

      <h1 class="mt-4 text-2xl font-semibold tracking-tight">
        Edit Shipment
      </h1>
      <p class="text-sm text-muted-foreground">
        Update shipment details and PO allocations.
      </p>
    </div>

    <form class="space-y-6" @submit.prevent="submit">
      <!-- Shipment Information -->
      <section class="rounded-lg border bg-card">
        <div class="border-b p-5">
          <h2 class="font-semibold">Shipment Information</h2>
        </div>

        <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
          <div class="space-y-2">
            <label class="text-sm font-medium">Shipment Number</label>
            <Input v-model="form.shipment_number" readonly />
            <p
              v-if="form.errors.shipment_number"
              class="text-sm text-destructive"
            >
              {{ form.errors.shipment_number }}
            </p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Invoice Number</label>
            <Input v-model="form.invoice_number" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">
              Purchase Orders in Containers
            </label>

            <div
              class="flex min-h-10 flex-wrap items-center gap-2 rounded-md border bg-muted/30 p-2"
            >
              <span
                v-if="selectedPos.length === 0"
                class="text-sm text-muted-foreground"
              >
                POs appear automatically when items are allocated.
              </span>

              <span
                v-for="po in selectedPos"
                :key="po.id"
                class="rounded-md border bg-background px-2 py-1 text-xs font-medium"
              >
                {{ po.po_number }}
              </span>
            </div>

            <p class="text-xs text-muted-foreground">
              Automatically aggregated from every container.
            </p>
            <p
              v-if="form.errors.purchase_order_ids"
              class="text-sm text-destructive"
            >
              {{ form.errors.purchase_order_ids }}
            </p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">BL Number</label>
            <Input v-model="form.bl_number" />
            <p
              v-if="form.errors.bl_number"
              class="text-sm text-destructive"
            >
              {{ form.errors.bl_number }}
            </p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Vessel Name</label>
            <Input v-model="form.vessel_name" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Voyage Number</label>
            <Input v-model="form.voyage_number" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Status Document</label>
            <Select v-model="form.status_document">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select status" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Status Document</SelectLabel>
                  <SelectItem
                    v-for="status in statusOptions"
                    :key="status"
                    :value="status"
                  >
                    {{ status }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Tracking Status</label>
            <Select v-model="form.tracking_status">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select tracking status" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Tracking Status</SelectLabel>
                  <SelectItem
                    v-for="status in trackingStatusOptions"
                    :key="status"
                    :value="status"
                  >
                    {{ status }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
        </div>
      </section>

      <!-- Tabs -->
      <section class="rounded-lg border bg-card p-5">
        <Tabs default-value="containers" class="w-full">
          <TabsList class="flex h-auto flex-wrap">
            <TabsTrigger value="containers">
              Containers & Items
            </TabsTrigger>
            <TabsTrigger value="schedule">
              Shipment Schedule
            </TabsTrigger>
            <TabsTrigger value="related">
              Related Information
            </TabsTrigger>
          </TabsList>

          <!-- Containers -->
          <TabsContent value="containers" class="space-y-5 pt-5">
            <div
              class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
              <div>
                <h2 class="font-semibold">Container Allocation</h2>
                <p class="text-sm text-muted-foreground">
                  Each PO item occupies one table row. Adding an item
                  does not create another container.
                </p>
              </div>

              <Button
                type="button"
                variant="outline"
                @click="addContainer"
              >
                <Plus class="mr-2 h-4 w-4" />
                Add Container
              </Button>
            </div>

            <div
              v-for="(container, containerIndex) in form.containers"
              :key="containerIndex"
              class="space-y-5 rounded-xl border p-4"
            >
              <div class="flex items-center justify-between">
                <h3 class="font-semibold">
                  Container {{ containerIndex + 1 }}
                </h3>

                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  :disabled="form.containers.length <= 1"
                  aria-label="Remove container"
                  @click="removeContainer(containerIndex)"
                >
                  <Trash2 class="h-4 w-4 text-destructive" />
                </Button>
              </div>

              <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                  <label class="text-sm font-medium">
                    Container Number
                  </label>
                  <Input
                    v-model="container.container_number"
                    placeholder="e.g. MSCU1234567"
                  />
                  <p
                    v-if="form.errors[`containers.${containerIndex}.container_number`]"
                    class="text-xs text-destructive"
                  >
                    {{ form.errors[`containers.${containerIndex}.container_number`] }}
                  </p>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium">
                    Container Seal Number
                  </label>
                  <Input
                    v-model="container.container_seal_number"
                    placeholder="Enter seal number"
                  />
                  <p
                    v-if="form.errors[`containers.${containerIndex}.container_seal_number`]"
                    class="text-xs text-destructive"
                  >
                    {{ form.errors[`containers.${containerIndex}.container_seal_number`] }}
                  </p>
                </div>
              </div>

              <!-- PO items table -->
              <div class="space-y-4 border-t pt-4">
                <div
                  class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                  <div>
                    <h4 class="font-medium">PO Line Items</h4>
                    <p class="text-sm text-muted-foreground">
                      Add PO items as individual rows in this table.
                    </p>
                  </div>

                  <Button
                    type="button"
                    variant="outline"
                    @click="addContainerItem(containerIndex)"
                  >
                    <Plus class="mr-2 h-4 w-4" />
                    Add PO Item
                  </Button>
                </div>

                <div class="w-full overflow-x-auto rounded-md border">
                  <Table class="w-full min-w-[1150px]">
                    <TableHeader>
                      <TableRow>
                        <TableHead class="w-12">No.</TableHead>
                        <TableHead class="min-w-[220px]">
                          Purchase Order
                        </TableHead>
                        <TableHead class="min-w-[320px]">
                          Purchase Order Item
                        </TableHead>
                        <TableHead class="w-[180px]">
                          Container Quantity
                        </TableHead>
                        <TableHead class="w-[120px]">
                          Unit
                        </TableHead>
                        <TableHead class="w-[72px] text-right">
                          Action
                        </TableHead>
                      </TableRow>
                    </TableHeader>

                    <TableBody>
                      <TableRow
                        v-for="(row, itemIndex) in container.items"
                        :key="`${containerIndex}-${itemIndex}`"
                      >
                        <TableCell
                          class="align-middle text-muted-foreground"
                        >
                          {{ itemIndex + 1 }}
                        </TableCell>

                        <TableCell class="align-middle">
                          <select
                            :value="row.purchase_order_id ?? ''"
                            class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            @change="onPurchaseOrderChange(
                              row,
                              ($event.target as HTMLSelectElement).value
                            )"
                          >
                            <option value="">Select PO</option>
                            <option
                              v-for="po in purchaseOrders"
                              :key="po.id"
                              :value="String(po.id)"
                            >
                              {{ po.po_number }}
                            </option>
                          </select>
                        </TableCell>

                        <TableCell class="align-middle">
                          <select
                            :value="row.purchase_order_item_id ?? ''"
                            :disabled="!row.purchase_order_id"
                            class="flex h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50"
                            @change="onPoItemChange(
                              row,
                              ($event.target as HTMLSelectElement).value
                            )"
                          >
                            <option value="">Select PO Item</option>
                            <option
                              v-for="item in poItemsForOrder(row.purchase_order_id).filter(
                                (item) =>
                                  Number(item.id) === Number(row.purchase_order_item_id) ||
                                  remainingQty(item, containerIndex, itemIndex) > 0
                              )"
                              :key="item.id"
                              :value="String(item.id)"
                            >
                              {{ item.product_code }} —
                              {{ item.product_name }}
                              (Remaining:
                              {{ formatNumber(remainingQty(item, containerIndex, itemIndex)) }}
                              {{ item.unit }})
                            </option>
                          </select>

                          <p
                            v-if="row.purchase_order_item_id && !rowPoItem(row)"
                            class="mt-1 text-xs text-destructive"
                          >
                            PO item not found in the available data.
                          </p>
                        </TableCell>

                        <TableCell class="align-middle">
                          <Input
                            :model-value="row.quantity ?? ''"
                            @update:model-value="
                              row.quantity = $event === '' ? null : Number($event)
                            "
                            type="number"
                            min="0.01"
                            step="0.01"
                            :max="
                              rowPoItem(row)
                                ? remainingQty(rowPoItem(row)!, containerIndex, itemIndex)
                                : undefined
                            "
                            :disabled="!row.purchase_order_item_id"
                            placeholder="0"
                            class="min-w-[120px]"
                          />
                          <p
                            v-if="
                              rowPoItem(row) &&
                              Number(row.quantity) >
                                remainingQty(rowPoItem(row)!, containerIndex, itemIndex)
                            "
                            class="mt-1 text-xs text-destructive"
                          >
                            Quantity exceeds the available amount.
                          </p>
                        </TableCell>

                        <TableCell class="align-middle">
                          <span
                            v-if="rowPoItem(row)"
                            class="text-sm"
                          >
                            {{ rowPoItem(row)?.unit }}
                          </span>
                          <span
                            v-else
                            class="text-sm text-muted-foreground"
                          >
                            —
                          </span>
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
                        <TableCell
                          :colspan="6"
                          class="h-24 text-center text-muted-foreground"
                        >
                          No PO items assigned to this container.
                          Click “Add PO Item” to add a row.
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

            <div
              class="flex flex-wrap justify-between gap-3 rounded-lg bg-muted/30 p-4"
            >
              <div class="text-sm">
                <span class="text-muted-foreground">POs included:</span>
                <strong>{{ selectedPos.length }}</strong>
              </div>
              <div class="text-sm">
                <span class="text-muted-foreground">Containers:</span>
                <strong>{{ form.containers.length }}</strong>
              </div>
            </div>

            <p class="text-xs text-muted-foreground">
              PO quantities, product details, and total prices are not
              edited here. The backend must validate and recalculate
              backorder quantities inside a database transaction.
            </p>
          </TabsContent>

          <!-- Shipment schedule -->
          <TabsContent value="schedule" class="pt-5">
            <h2 class="mb-4 font-semibold">Shipment Schedule</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <div class="space-y-2">
                <label class="text-sm font-medium">ETD</label>
                <Input v-model="form.etd" type="date" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium">ETA</label>
                <Input v-model="form.eta" type="date" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium">
                  Actual Departure (ATD)
                </label>
                <Input v-model="form.atd" type="date" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium">
                  Actual Arrival (ATA)
                </label>
                <Input v-model="form.ata" type="date" />
              </div>
            </div>
            <div class="mt-8 space-y-4">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <h3 class="font-semibold">Milestone</h3>
                  <p class="text-sm text-muted-foreground">Catat rencana dan realisasi tahapan shipment.</p>
                </div>
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

          <!-- Related information -->
          <TabsContent value="related" class="pt-5">
            <h2 class="mb-4 font-semibold">Related Information</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <div class="space-y-2">
                <label class="text-sm font-medium">Forwarder</label>
                <Select v-model="form.forwarder_id">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select forwarder" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Forwarder</SelectLabel>
                      <SelectItem
                        v-for="item in forwarderOptions"
                        :key="item.id"
                        :value="String(item.id)"
                      >
                        {{ item.forwarder_name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Shipping Line</label>
                <Select v-model="form.shipping_line_id">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select shipping line" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Shipping Line</SelectLabel>
                      <SelectItem
                        v-for="item in shippingLineOptions"
                        :key="item.id"
                        :value="String(item.id)"
                      >
                        {{ item.shipping_line_name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Port of Loading</label>
                <Select v-model="form.port_of_loading_id">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select port" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Port of Loading</SelectLabel>
                      <SelectItem
                        v-for="item in portOptions"
                        :key="item.id"
                        :value="String(item.id)"
                      >
                        {{ item.port_name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Port of Discharge</label>
                <Select v-model="form.port_of_discharge_id">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select port" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Port of Discharge</SelectLabel>
                      <SelectItem
                        v-for="item in portOptions"
                        :key="item.id"
                        :value="String(item.id)"
                      >
                        {{ item.port_name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Warehouse</label>
                <Select v-model="form.warehouse_id">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select warehouse" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Warehouse</SelectLabel>
                      <SelectItem
                        v-for="item in warehouseOptions"
                        :key="item.id"
                        :value="String(item.id)"
                      >
                        {{ item.warehouse_name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2 sm:col-span-2 lg:col-span-3">
                <label class="text-sm font-medium">Remarks</label>
                <Textarea v-model="form.remarks" />
              </div>
            </div>
          </TabsContent>
        </Tabs>
      </section>

      <!-- Actions -->
      <div class="flex flex-wrap justify-end gap-3">
        <Button type="button" variant="outline" as-child>
          <Link href="/shipments">Cancel</Link>
        </Button>

        <Button
          type="submit"
          :disabled="
            form.processing ||
            hasOverAllocation ||
            hasInvalidItems
          "
        >
          {{ form.processing ? 'Saving...' : 'Save Changes' }}
        </Button>
      </div>
    </form>
  </div>
</template>