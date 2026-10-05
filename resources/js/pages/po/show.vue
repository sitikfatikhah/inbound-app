<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Pencil } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { computed } from 'vue'
import {
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs'
import {
    Field,
    FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSet,
} from '@/components/ui/field'
import Tabs from '@/components/ui/tabs/Tabs.vue'

interface Supplier {
    id: number | string
    supplier_name: string
}

interface PurchaseOrderItem {
    id: number
    product_id: number | string
    product_code: string
    product_name: string
    quantity: number | string
    unit: string | null
    hs_code: string | null
    import_duty: number | string | null
    facility: string | null
    unit_price: number | string
    total_price: number | string
}

interface PurchaseOrder {
    id: number
    supplier_id: number | string | null
    supplier?: Supplier | null
    category: string | null
    po_number: string
    po_date: string | null
    currency: string | null
    exchange_rate: number | string | null
    incoterm: string | null
    payment_term: string | null
    delivery_date: string | null
    arrival_date: string | null
    status: string | null
    items?: PurchaseOrderItem[]
}

const props = defineProps<{
    purchaseOrder: PurchaseOrder
}>()
const grandTotal = computed(() => {
    return (props.purchaseOrder.items ?? []).reduce((total, item) => {
        return total + Number(item.total_price || 0)
    }, 0)
})
const statusLabel = (status: string | null) =>
    status
        ? status
            .toLowerCase()
            .split('_')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ')
        : '-'

const dateLabel = (date: string | null) => date ? date.slice(0, 10) : '-'

const displayValue = (value: string | number | null) => value ?? '-'
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <Link href="/po"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground mt-4">
                    <ArrowLeft class="h-4 w-4" />
                    Back to Purchase Orders
                </Link>

                <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                    Purchase Order {{ purchaseOrder.po_number }}
                </h1>

                <p class="text-sm text-muted-foreground">
                    View the purchase order details.
                </p>
            </div>

            <Button as-child class="h-10 mt-4">
                <Link :href="`/po/${purchaseOrder.id}/edit`">
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit Purchase Order
                </Link>
            </Button>
        </div>

        <section class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">Purchase Order Information</h2>
            </div>

            <dl class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm text-muted-foreground">Purchase Order Number</dt>
                    <dd class="mt-1 font-medium">{{ purchaseOrder.po_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Supplier</dt>
                    <dd class="mt-1 font-medium">{{ purchaseOrder.supplier?.supplier_name || '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Category</dt>
                    <dd class="mt-1 font-medium">{{ displayValue(purchaseOrder.category) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Purchase Order Date</dt>
                    <dd class="mt-1 font-medium">{{ dateLabel(purchaseOrder.po_date) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Currency</dt>
                    <dd class="mt-1 font-medium">{{ displayValue(purchaseOrder.currency) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Exchange Rate</dt>
                    <dd class="mt-1 font-medium">{{ displayValue(purchaseOrder.exchange_rate) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Incoterm</dt>
                    <dd class="mt-1 font-medium">{{ displayValue(purchaseOrder.incoterm) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Payment Term</dt>
                    <dd class="mt-1 font-medium">{{ displayValue(purchaseOrder.payment_term) }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-lg border bg-card">
            <div class="flex w-full flex-col gap-6 mx-4 rounded-lg border bg-card p-6">
                <Tabs>
                    <TabsList>
                        <TabsTrigger value="schedule">
                            Schedule
                        </TabsTrigger>
                        <TabsTrigger value="item">
                            Item
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="item">

                        <div class="rounded-lg border bg-card">

                            <!-- Header -->

                            <div class="border-b p-6 flex items-center justify-between">
                                <div>
                                    <h2 class="font-semibold">
                                        Purchase Order Items
                                    </h2>

                                    <p class="text-sm text-muted-foreground">
                                        Add products included in this purchase order.
                                    </p>
                                </div>
                            </div>

                            <!-- Table -->

                            <div class="p-6 overflow-x-auto">

                                <Table>

                                    <TableHeader>
                                        <TableRow>

                                            <TableHead class="w-[220px]">
                                                Product
                                            </TableHead>

                                            <TableHead>
                                                Quantity
                                            </TableHead>

                                            <TableHead>
                                                Unit
                                            </TableHead>

                                            <TableHead>
                                                Facility
                                            </TableHead>

                                            <TableHead>
                                                Import Duty
                                            </TableHead>

                                            <TableHead>
                                                Unit Price
                                            </TableHead>

                                            <TableHead>
                                                Total Price
                                            </TableHead>

                                            <TableHead class="w-[60px]">
                                            </TableHead>

                                        </TableRow>
                                    </TableHeader>

                                    <TableBody>
                                        <TableRow v-for="item in purchaseOrder.items ?? []" :key="item.id">
                                            <TableCell>
                                                <div class="font-medium">
                                                    {{ item.product_code }}
                                                </div>

                                                <div class="text-sm text-muted-foreground">
                                                    {{ item.product_name }}
                                                </div>
                                            </TableCell>

                                            <TableCell>
                                                {{ item.quantity }}
                                            </TableCell>

                                            <TableCell>
                                                {{ item.unit || '-' }}
                                            </TableCell>

                                            <TableCell>
                                                {{ item.facility || '-' }}
                                            </TableCell>

                                            <TableCell>
                                                {{ item.import_duty ?? 0 }}%
                                            </TableCell>

                                            <TableCell>
                                                {{ Number(item.unit_price).toLocaleString() }}
                                            </TableCell>

                                            <TableCell>
                                                {{ Number(item.total_price).toLocaleString() }}
                                            </TableCell>
                                        </TableRow>

                                        <TableRow v-if="!purchaseOrder.items?.length">
                                            <TableCell colspan="7" class="py-10 text-center text-muted-foreground">
                                                No items found.
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>

                                    <!-- Total -->

                                    <TableFooter>

                                        <TableRow>
                                            <TableCell colspan="6" class="text-right font-semibold">
                                                Grand Total
                                            </TableCell>

                                            <TableCell class="font-semibold">
                                                {{ grandTotal.toLocaleString() }}
                                            </TableCell>
                                        </TableRow>

                                    </TableFooter>

                                </Table>

                            </div>

                        </div>

                    </TabsContent>
                    <TabsContent value="schedule">
                        <div class="w-full gap-6">
                            <Card class="w-full p-2">
                                <form>
                                    <FieldGroup>
                                        <FieldSet class="mx-4">
                                            <FieldLegend>Schedule Information</FieldLegend>
                                            <FieldDescription>
                                                Schedule Shipment Information
                                            </FieldDescription>
                                            <FieldGroup class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 pb-4">
                                                <Field>
                                                    <div>
                                                        <dt class="text-sm text-muted-foreground">Estimated Time of
                                                            Arrival (ETA)</dt>
                                                        <dd class="mt-1 font-medium">{{
                                                            dateLabel(purchaseOrder.arrival_date) }}</dd>
                                                    </div>
                                                </Field>
                                                <Field>
                                                    <div>
                                                        <dt class="text-sm text-muted-foreground">Estimated Time of
                                                            Departure (ETD)</dt>
                                                        <dd class="mt-1 font-medium">{{
                                                            dateLabel(purchaseOrder.delivery_date) }}</dd>
                                                    </div>
                                                </Field>

                                            </FieldGroup>
                                        </FieldSet>
                                    </FieldGroup>
                                </form>
                            </Card>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </section>

        <section class="rounded-lg border bg-card">
            <div class="border-b p-6">
                <h2 class="font-semibold">Related Information</h2>
            </div>

            <dl class="p-6">
                <dt class="text-sm text-muted-foreground">Status</dt>
                <dd class="mt-1 font-medium">{{ statusLabel(purchaseOrder.status) }}</dd>
            </dl>
        </section>
    </div>
</template>
