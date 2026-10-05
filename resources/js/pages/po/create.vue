<script setup lang="ts">
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Tabs,TabsContent,TabsList,TabsTrigger } from '@/components/ui/tabs'
import { Table, TableBody,TableCell,TableFooter,TableHead,TableHeader,TableRow, } from '@/components/ui/table'
import { Field,FieldDescription,FieldGroup,FieldLabel,FieldLegend,FieldSet } from '@/components/ui/field'
import { Card } from '@/components/ui/card'

interface Supplier {
    id: string | number
    supplier_name: string
}

interface Product {
    id: string | number
    product_code: string
    product_name: string
    unit?: string | null
    hs_code?: string | null
    facility?: string | null
    import_duty?: string | number | null
}

interface PurchaseOrderItem {
    product_id: string | number
    product_code: string
    product_name: string
    quantity: number | string
    unit: string
    hs_code: string
    import_duty: number | string
    facility: string
    unit_price: number | string
    total_price: number | string
}

const props = defineProps<{
    suppliers?: Supplier[]
    products?: Product[]
}>()

const form = useForm({
    po_number: '',
    supplier_id: '',
    category: '',
    po_date: '',
    currency: '',
    exchange_rate: '',
    incoterm: '',
    payment_term: '',
    delivery_date: '',
    arrival_date: '',
    status: '',

    items: [] as PurchaseOrderItem[],
})

const createEmptyItem = (): PurchaseOrderItem => ({
    product_id: '',
    product_code: '',
    product_name: '',
    quantity: 1,
    unit: '',
    hs_code: '',
    import_duty: 0,
    facility: '',
    unit_price: 0,
    total_price: 0,
})

const addItem = () => {
    form.items.push(createEmptyItem())
}

const removeItem = (index: number) => {
    form.items.splice(index, 1)
}

const selectProduct = (item: PurchaseOrderItem) => {
    const product = props.products?.find(
        (product) => String(product.id) === String(item.product_id)
    )

    if (!product) {
        item.product_code = ''
        item.product_name = ''
        item.unit = ''
        item.facility = ''
        item.import_duty = 0
        return
    }

    item.product_code = product.product_code
    item.product_name = product.product_name
    item.unit = product.unit ?? ''
    item.facility = product.facility ?? ''
    item.import_duty = Number(product.import_duty ?? 0)

    calculateItemTotal(item)
}

const calculateItemTotal = (item: PurchaseOrderItem) => {
    item.total_price =
        Number(item.quantity || 0) *
        Number(item.unit_price || 0)
}

const grandTotal = computed(() => {
    return form.items.reduce(
        (total, item) =>
            total + Number(item.total_price || 0),
        0
    )
})

const supplierIdOptions = computed(() =>
    (props.suppliers ?? []).map((supplier) => ({
        value: supplier.id,
        label: supplier.supplier_name,
    }))
)

const statusOptions = [
    { label: 'Draft', value: 'DRAFT' },
    { label: 'Booked', value: 'BOOKED' },
    { label: 'In Transit', value: 'IN_TRANSIT' },
    { label: 'Arrived at Port', value: 'ARRIVED_PORT' },
    { label: 'Customs Process', value: 'CUSTOMS_PROCESS' },
    { label: 'Customs Cleared', value: 'CUSTOMS_CLEARED' },
    { label: 'DO Released', value: 'DO_RELEASED' },
    { label: 'Delivery Process', value: 'DELIVERY_PROCESS' },
    { label: 'Delivered', value: 'DELIVERED' },
    { label: 'Completed', value: 'COMPLETED' },
    { label: 'On Hold', value: 'ON_HOLD' },
    { label: 'Cancelled', value: 'CANCELLED' },
]

const incotermOptions = [
    { label: 'FCA - Free Carrier', value: 'FCA' },
    { label: 'CPT - Carriage Paid To', value: 'CPT' },
    { label: 'CIP - Carriage and Insurance Paid To', value: 'CIP' },
    { label: 'DAP - Delivered At Place', value: 'DAP' },
    { label: 'DPU - Delivered at Place Unloaded', value: 'DPU' },
    { label: 'DDP - Delivered Duty Paid', value: 'DDP' },
    { label: 'FAS - Free Alongside Ship', value: 'FAS' },
    { label: 'FOB - Free On Board', value: 'FOB' },
    { label: 'CFR - Cost and Freight', value: 'CFR' },
    { label: 'CIF - Cost, Insurance and Freight', value: 'CIF' },
]

const categoryOptions = [
    { label: 'Local', value: 'LOCAL' },
    { label: 'Import', value: 'IMPORT' },
]

const handleSubmit = () => {
    form.post('/po', {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <div class="w-full mx-4 space-y-6 rounded-lg border bg-card p-6">

        <!-- Header -->
        <div>
            <Link href="/po" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Purchase Order
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Purchase Order
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new purchase order.
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-6">

            <!-- Purchase Order Information -->
            <div class="rounded-lg border bg-card mx-4">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Purchase Order Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic purchase order information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Purchase Order Number -->

                    <!-- Purchase Order Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Purchase Order Number</label>
                        <Input v-model="form.po_number" placeholder="Enter purchase order number" />
                    </div>

                    <!-- supplier id-->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Supplier ID</label>
                        <select v-model="form.supplier_id"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm">
                            <option v-for="supplier in supplierIdOptions" :key="supplier.value" :value="supplier.value">
                                {{ supplier.label }}
                            </option>
                        </select>
                    </div>

                    <!-- category-->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Category</label>
                        <select v-model="form.category"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm">
                            <option v-for="category in categoryOptions" :key="category.value" :value="category.value">
                                {{ category.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Po Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Purchase Order Date</label>
                        <Input v-model="form.po_date" type="date" />
                    </div>

                    <!-- Currency -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Currency</label>
                        <Input v-model="form.currency" placeholder="Enter currency" />
                    </div>
                    <!-- Exchange Rate -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Exchange Rate</label>
                        <Input v-model="form.exchange_rate" placeholder="Enter exchange rate" />
                    </div>
                    <!-- Payment Term -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Payment Term</label>
                        <Input v-model="form.payment_term" placeholder="Enter payment term" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Incoterm</label>

                        <select v-model="form.incoterm"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm">
                            <option v-for="incoterm in incotermOptions" :key="incoterm.value" :value="incoterm.value">
                                {{ incoterm.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex w-full flex-col gap-6 mx-4 rounded-lg border bg-card p-6">
                <Tabs default-value="">
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

                                <Button type="button" variant="outline" @click="addItem">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Item
                                </Button>

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

                                        <TableRow v-for="(item, index) in form.items" :key="index">

                                            <!-- Product -->

                                            <TableCell>

                                                <select v-model="item.product_id" @change="selectProduct(item)"
                                                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm">

                                                    <option value="">
                                                        Select Product
                                                    </option>

                                                    <option v-for="product in props.products ?? []" :key="product.id"
                                                        :value="product.id">
                                                        {{ product.product_code }}
                                                        -
                                                        {{ product.product_name }}
                                                    </option>

                                                </select>

                                            </TableCell>

                                            <!-- Quantity -->

                                            <TableCell>

                                                <Input v-model.number="item.quantity" type="number" min="0.01"
                                                    step="0.01" class="w-24" @input="calculateItemTotal(item)" />

                                            </TableCell>

                                            <!-- Unit -->

                                            <TableCell>

                                                <Input v-model="item.unit" class="w-24" placeholder="PCS" />

                                            </TableCell>

                                            <!-- Facility -->

                                            <TableCell>

                                                <Input v-model="item.facility" class="w-28" placeholder="Facility" />

                                            </TableCell>

                                            <!-- Import Duty -->

                                            <TableCell>

                                                <div class="flex items-center gap-1">

                                                    <Input v-model.number="item.import_duty" type="number" min="0"
                                                        max="100" step="0.01" class="w-24" />

                                                    <span class="text-sm text-muted-foreground">
                                                        %
                                                    </span>

                                                </div>

                                            </TableCell>

                                            <!-- Unit Price -->

                                            <TableCell>

                                                <Input v-model.number="item.unit_price" type="number" min="0"
                                                    step="0.01" class="w-32" @input="calculateItemTotal(item)" />

                                            </TableCell>

                                            <!-- Total -->

                                            <TableCell>

                                                <Input :model-value="item.total_price" type="number" readonly
                                                    class="w-32 bg-muted" />

                                            </TableCell>

                                            <!-- Remove -->

                                            <TableCell>

                                                <Button type="button" variant="ghost" size="icon"
                                                    @click="removeItem(index)">
                                                    <Trash2 class="h-4 w-4 text-destructive" />
                                                </Button>

                                            </TableCell>

                                        </TableRow>

                                        <!-- Empty State -->

                                        <TableRow v-if="form.items.length === 0">

                                            <TableCell colspan="9" class="text-center py-10 text-muted-foreground">
                                                No items added yet.
                                                Click "Add Item" to add a product.
                                            </TableCell>

                                        </TableRow>

                                    </TableBody>

                                    <!-- Total -->

                                    <TableFooter>

                                        <TableRow>

                                            <TableCell colspan="7" class="text-right font-semibold">
                                                Grand Total
                                            </TableCell>

                                            <TableCell class="font-semibold">

                                                {{ grandTotal.toLocaleString() }}

                                            </TableCell>

                                            <TableCell></TableCell>

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
                                            <FieldLegend>Payment Method</FieldLegend>
                                            <FieldDescription>
                                                All transactions are secure and encrypted
                                            </FieldDescription>
                                            <FieldGroup class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 pb-4">
                                                <Field>
                                                    <FieldLabel for="arrival_date" class="space-y-2">
                                                        Arrival Date
                                                    </FieldLabel>
                                                    <Input  v-model="form.arrival_date" placeholder="Enter Arrival Date"
                                                        required />
                                                </Field>
                                                <Field>
                                                    <FieldLabel for="arrival_date" class="space-y-2">
                                                        Arrival Date
                                                    </FieldLabel>
                                                    <Input  v-model="form.arrival_date" placeholder="Enter Arrival Date"
                                                        required />
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
            <!-- Actions -->
            <div class="flex justify-end gap-4 p-6">

                <Button type="button" variant="outline" as-child>
                    <Link href="/po">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{
                        form.processing
                            ? 'Saving...'
                            : 'Save Purchase Order'
                    }}
                </Button>


            </div>

        </form>
    </div>
</template>

<style></style>