<script setup lang="ts">
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
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
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

interface Suppliers {
    id: number | string
    supplier_name: string
}

interface Product {
    id: number | string
    product_code: string
    product_name: string
    unit?: string | null
    hs_code?: string | null
    import_duty?: number | string | null
    facility?: string | null
}

interface PurchaseOrderItem {
    id?: number | string
    product_id: number | string | null
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

interface PurchaseOrders {
    id: number
    supplier_id: number | string | null
    category: string | null
    po_number: string
    po_date: string | Date | null
    currency: string | null
    exchange_rate: number | string | null
    incoterm: string | null
    payment_term: string | null
    delivery_date: string | Date | null
    arrival_date: string | Date | null
    status: string | null
    items?: PurchaseOrderItem[]
}

const props = defineProps<{
    purchaseOrder: PurchaseOrders
    suppliers?: Suppliers[]
    products?: Product[]
}>()


/**
 * Convert database date to HTML date input format.
 */
const toDateInputValue = (
    value: string | Date | null | undefined
): string => {
    if (!value) {
        return ''
    }

    if (value instanceof Date) {
        return value.toISOString().slice(0, 10)
    }

    return String(value).slice(0, 10)
}

/**
 * Form
 */
const form = useForm({
    po_number: props.purchaseOrder.po_number ?? '',

    supplier_id: props.purchaseOrder.supplier_id ?? '',

    category: props.purchaseOrder.category ?? '',

    po_date: toDateInputValue(
        props.purchaseOrder.po_date
    ),

    currency: props.purchaseOrder.currency ?? '',

    exchange_rate:
        props.purchaseOrder.exchange_rate ?? '',

    incoterm:
        props.purchaseOrder.incoterm ?? '',

    payment_term:
        props.purchaseOrder.payment_term ?? '',

    delivery_date: toDateInputValue(
        props.purchaseOrder.delivery_date
    ),

    arrival_date: toDateInputValue(
        props.purchaseOrder.arrival_date
    ),

    status:
        props.purchaseOrder.status ?? 'DRAFT',

    items: (props.purchaseOrder.items ?? []).map(
        (item) => ({
            id: item.id,

            product_id:
                item.product_id ?? null,

            product_code:
                item.product_code ?? '',

            product_name:
                item.product_name ?? '',

            quantity:
                item.quantity ?? 1,

            unit:
                item.unit ?? '',

            hs_code:
                item.hs_code ?? '',

            import_duty:
                item.import_duty ?? 0,

            facility:
                item.facility ?? '',

            unit_price:
                item.unit_price ?? 0,

            total_price:
                item.total_price ??
                Number(item.quantity ?? 0) *
                    Number(item.unit_price ?? 0),
        })
    ),
})

/**
 * Add new item
 */
const addItem = () => {
    form.items.push({
        id: undefined,

        product_id: null,

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
}

/**
 * Remove item
 */
const removeItem = (index: number) => {
    form.items.splice(index, 1)
}

/**
 * Select product
 */
const selectProduct = (
    item: PurchaseOrderItem
) => {
    const product =
        props.products?.find(
            (product) =>
                String(product.id) ===
                String(item.product_id)
        )

    if (!product) {
        item.product_code = ''
        item.product_name = ''
        item.unit = ''
        item.hs_code = ''
        item.facility = ''
        item.import_duty = 0

        calculateItemTotal(item)

        return
    }

    item.product_code =
        product.product_code

    item.product_name =
        product.product_name

    item.unit =
        product.unit ?? ''

    item.hs_code =
        product.hs_code ?? ''

    item.facility =
        product.facility ?? ''

    item.import_duty =
        Number(product.import_duty ?? 0)

    calculateItemTotal(item)
}

/**
 * Calculate item total
 */
const calculateItemTotal = (
    item: PurchaseOrderItem
) => {
    const quantity =
        Number(item.quantity || 0)

    const unitPrice =
        Number(item.unit_price || 0)

    item.total_price =
        quantity * unitPrice
}

/**
 * Grand total
 */
const grandTotal = computed(() => {
    return form.items.reduce(
        (total, item) => {
            return (
                total +
                Number(
                    item.total_price || 0
                )
            )
        },
        0
    )
})

/**
 * Supplier options
 */
const supplierIdOptions =
    computed(() =>
        (props.suppliers ?? []).map(
            (supplier) => ({
                value: supplier.id,
                label:
                    supplier.supplier_name,
            })
        )
    )

/**
 * Category options
 */
const categoryOptions = [
    {
        label: 'Import',
        value: 'IMPORT',
    },
    {
        label: 'Local',
        value: 'LOCAL',
    },
]

/**
 * Status options
 */
const statusOptions = [
    {
        label: 'Draft',
        value: 'DRAFT',
    },
    {
        label: 'Booked',
        value: 'BOOKED',
    },
    {
        label: 'In Transit',
        value: 'IN_TRANSIT',
    },
    {
        label: 'Arrived at Port',
        value: 'ARRIVED_PORT',
    },
    {
        label: 'Customs Process',
        value: 'CUSTOMS_PROCESS',
    },
    {
        label: 'Customs Cleared',
        value: 'CUSTOMS_CLEARED',
    },
    {
        label: 'DO Released',
        value: 'DO_RELEASED',
    },
    {
        label: 'Delivery Process',
        value: 'DELIVERY_PROCESS',
    },
    {
        label: 'Delivered',
        value: 'DELIVERED',
    },
    {
        label: 'Completed',
        value: 'COMPLETED',
    },
    {
        label: 'On Hold',
        value: 'ON_HOLD',
    },
    {
        label: 'Cancelled',
        value: 'CANCELLED',
    },
]

/**
 * Incoterm options
 */
const incotermOptions = [
    {
        label: 'FCA - Free Carrier',
        value: 'FCA',
    },
    {
        label: 'CPT - Carriage Paid To',
        value: 'CPT',
    },
    {
        label: 'CIP - Carriage and Insurance Paid To',
        value: 'CIP',
    },
    {
        label: 'DAP - Delivered At Place',
        value: 'DAP',
    },
    {
        label: 'DPU - Delivered at Place Unloaded',
        value: 'DPU',
    },
    {
        label: 'DDP - Delivered Duty Paid',
        value: 'DDP',
    },
    {
        label: 'FAS - Free Alongside Ship',
        value: 'FAS',
    },
    {
        label: 'FOB - Free On Board',
        value: 'FOB',
    },
    {
        label: 'CFR - Cost and Freight',
        value: 'CFR',
    },
    {
        label: 'CIF - Cost, Insurance and Freight',
        value: 'CIF',
    },
]

/**
 * Submit update
 */
const handleSubmit = () => {
    form.put(
        `/po/${props.purchaseOrder.id}`,
        {
            preserveScroll: true,

            onSuccess: () => {
                console.log(
                    'Purchase Order berhasil diperbarui'
                )
            },

            onError: (errors) => {
                console.error(
                    'Validation errors:',
                    errors
                )
            },
        }
    )
}
</script>

<template>
    <div
        class="mx-4 w-full space-y-6 rounded-lg border bg-card p-6"
    >
        <!-- Header -->
        <div>
            <Link
                href="/po"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />

                Back to Purchase Order
            </Link>

            <h1
                class="mt-4 text-2xl font-semibold tracking-tight"
            >
                Edit Purchase Order
            </h1>

            <p
                class="text-sm text-muted-foreground"
            >
                Edit the purchase order details.
            </p>
        </div>

        <!-- Form -->
        <form
            @submit.prevent="handleSubmit"
            class="space-y-6"
        >
            <!-- Purchase Order Information -->
            <div
                class="mx-4 rounded-lg border bg-card"
            >
                <div
                    class="border-b p-6"
                >
                    <h2
                        class="font-semibold"
                    >
                        Purchase Order Information
                    </h2>

                    <p
                        class="text-sm text-muted-foreground"
                    >
                        Enter the basic purchase order information.
                    </p>
                </div>

                <div
                    class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- PO Number -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Purchase Order Number
                        </label>

                        <Input
                            v-model="form.po_number"
                            placeholder="Enter purchase order number"
                        />

                        <p
                            v-if="form.errors.po_number"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.po_number }}
                        </p>
                    </div>

                    <!-- Supplier -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Supplier
                        </label>

                        <select
                            v-model="form.supplier_id"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="">
                                Select supplier
                            </option>

                            <option
                                v-for="supplier in supplierIdOptions"
                                :key="supplier.value"
                                :value="supplier.value"
                            >
                                {{ supplier.label }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.supplier_id"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.supplier_id }}
                        </p>
                    </div>

                    <!-- Category -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Category
                        </label>

                        <select
                            v-model="form.category"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="">
                                Select category
                            </option>

                            <option
                                v-for="category in categoryOptions"
                                :key="category.value"
                                :value="category.value"
                            >
                                {{ category.label }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.category"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <!-- PO Date -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Purchase Order Date
                        </label>

                        <Input
                            v-model="form.po_date"
                            type="date"
                        />

                        <p
                            v-if="form.errors.po_date"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.po_date }}
                        </p>
                    </div>

                    <!-- Currency -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Currency
                        </label>

                        <Input
                            v-model="form.currency"
                            placeholder="Enter currency"
                        />

                        <p
                            v-if="form.errors.currency"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.currency }}
                        </p>
                    </div>

                    <!-- Exchange Rate -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Exchange Rate
                        </label>

                        <Input
                            v-model="form.exchange_rate"
                            type="number"
                            step="0.01"
                            placeholder="Enter exchange rate"
                        />

                        <p
                            v-if="form.errors.exchange_rate"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.exchange_rate }}
                        </p>
                    </div>

                    <!-- Payment Term -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Payment Term
                        </label>

                        <Input
                            v-model="form.payment_term"
                            placeholder="Enter payment term"
                        />

                        <p
                            v-if="form.errors.payment_term"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.payment_term }}
                        </p>
                    </div>

                    <!-- Incoterm -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Incoterm
                        </label>

                        <select
                            v-model="form.incoterm"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="">
                                Select incoterm
                            </option>

                            <option
                                v-for="incoterm in incotermOptions"
                                :key="incoterm.value"
                                :value="incoterm.value"
                            >
                                {{ incoterm.label }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.incoterm"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.incoterm }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Schedule / Items -->
            <div
                class="mx-4 mb-6 rounded-lg border bg-card"
            >
                <Tabs
                    default-value="item"
                    class="w-full"
                >
                    <!-- Tabs Header -->
                    <div
                        class="border-b px-6 pt-4"
                    >
                        <TabsList>
                            <TabsTrigger
                                value="schedule"
                            >
                                Schedule
                            </TabsTrigger>

                            <TabsTrigger
                                value="item"
                            >
                                Items
                            </TabsTrigger>
                        </TabsList>
                    </div>

                    <!-- Schedule Tab -->
                    <TabsContent
                        value="schedule"
                        class="p-6"
                    >
                        <div
                            class="grid gap-6 md:grid-cols-2"
                        >
                            <!-- Delivery Date -->
                            <div
                                class="space-y-2"
                            >
                                <label
                                    class="text-sm font-medium"
                                >
                                    Delivery Date
                                </label>

                                <Input
                                    v-model="form.delivery_date"
                                    type="date"
                                />

                                <p
                                    v-if="form.errors.delivery_date"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.delivery_date }}
                                </p>
                            </div>

                            <!-- Arrival Date -->
                            <div
                                class="space-y-2"
                            >
                                <label
                                    class="text-sm font-medium"
                                >
                                    Arrival Date
                                </label>

                                <Input
                                    v-model="form.arrival_date"
                                    type="date"
                                />

                                <p
                                    v-if="form.errors.arrival_date"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.arrival_date }}
                                </p>
                            </div>
                        </div>
                    </TabsContent>

                    <!-- Items Tab -->
                    <TabsContent
                        value="item"
                        class="w-full"
                    >
                        <div
                            class="rounded-lg border bg-card"
                        >
                            <!-- Header -->
                            <div
                                class="flex items-center justify-between border-b p-6"
                            >
                                <div>
                                    <h2
                                        class="font-semibold"
                                    >
                                        Purchase Order Items
                                    </h2>

                                    <p
                                        class="text-sm text-muted-foreground"
                                    >
                                        Add products included in this purchase order.
                                    </p>
                                </div>

                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="addItem"
                                >
                                    <Plus
                                        class="mr-2 h-4 w-4"
                                    />

                                    Add Item
                                </Button>
                            </div>

                            <!-- Table -->
                            <div
                                class="w-full overflow-x-auto p-6"
                            >
                                <Table
                                    class="min-w-[1200px]"
                                >
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead
                                                class="w-[250px]"
                                            >
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

                                            <TableHead
                                                class="w-[60px]"
                                            >
                                                Action
                                            </TableHead>
                                        </TableRow>
                                    </TableHeader>

                                    <TableBody>
                                        <!-- Items -->
                                        <TableRow
                                            v-for="(
                                                item,
                                                index
                                            ) in form.items"
                                            :key="
                                                item.id ??
                                                `new-${index}`
                                            "
                                        >
                                            <!-- Product -->
                                            <TableCell>
                                                <select
                                                    v-model="item.product_id"
                                                    @change="selectProduct(item)"
                                                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                                                >
                                                    <option value="">
                                                        Select Product
                                                    </option>

                                                    <option
                                                        v-for="product in props.products ?? []" :key="product.id" :value="product.id"
                                                    >
                                                        {{ product.product_code }}
                                                        -
                                                        {{ product.product_name }}
                                                    </option>
                                                </select>
                                            </TableCell>

                                            <!-- Quantity -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="
                                                        item.quantity
                                                    "
                                                    type="number"
                                                    min="0.01"
                                                    step="0.01"
                                                    class="w-24"
                                                    @input="
                                                        calculateItemTotal(
                                                            item
                                                        )
                                                    "
                                                />
                                            </TableCell>

                                            <!-- Unit -->
                                            <TableCell>
                                                <Input
                                                    v-model="
                                                        item.unit
                                                    "
                                                    class="w-24"
                                                    placeholder="PCS"
                                                />
                                            </TableCell>

                                            <!-- Facility -->
                                            <TableCell>
                                                <Input
                                                    v-model="
                                                        item.facility
                                                    "
                                                    class="w-28"
                                                    placeholder="Facility"
                                                />
                                            </TableCell>

                                            <!-- Import Duty -->
                                            <TableCell>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <Input
                                                        v-model.number="
                                                            item.import_duty
                                                        "
                                                        type="number"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        class="w-24"
                                                    />

                                                    <span
                                                        class="text-sm text-muted-foreground"
                                                    >
                                                        %
                                                    </span>
                                                </div>
                                            </TableCell>

                                            <!-- Unit Price -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="
                                                        item.unit_price
                                                    "
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="w-32"
                                                    @input="
                                                        calculateItemTotal(
                                                            item
                                                        )
                                                    "
                                                />
                                            </TableCell>

                                            <!-- Total -->
                                            <TableCell>
                                                <div
                                                    class="flex h-10 w-32 items-center rounded-md border bg-muted px-3 text-sm"
                                                >
                                                    {{
                                                        Number(
                                                            item.total_price ||
                                                                0
                                                        ).toLocaleString(
                                                            'id-ID'
                                                        )
                                                    }}
                                                </div>
                                            </TableCell>

                                            <!-- Remove -->
                                            <TableCell>
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="
                                                        removeItem(
                                                            index
                                                        )
                                                    "
                                                >
                                                    <Trash2
                                                        class="h-4 w-4 text-destructive"
                                                    />
                                                </Button>
                                            </TableCell>
                                        </TableRow>

                                        <!-- Empty State -->
                                        <TableRow
                                            v-if="
                                                form.items
                                                    .length ===
                                                0
                                            "
                                        >
                                            <TableCell
                                                colspan="8"
                                                class="py-10 text-center text-muted-foreground"
                                            >
                                                <div
                                                    class="flex flex-col items-center gap-2"
                                                >
                                                    <p>
                                                        No items added yet.
                                                    </p>

                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        @click="
                                                            addItem
                                                        "
                                                    >
                                                        <Plus
                                                            class="mr-2 h-4 w-4"
                                                        />

                                                        Add First Item
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>

                                    <!-- Total -->
                                    <TableFooter>
                                        <TableRow>
                                            <TableCell
                                                colspan="6"
                                                class="text-right font-semibold"
                                            >
                                                Grand Total
                                            </TableCell>

                                            <TableCell
                                                class="font-semibold"
                                            >
                                                {{
                                                    grandTotal.toLocaleString(
                                                        'id-ID'
                                                    )
                                                }}
                                            </TableCell>

                                            <TableCell></TableCell>
                                        </TableRow>
                                    </TableFooter>
                                </Table>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>

            <!-- Related Information -->
            <div
                class="mx-4 rounded-lg border bg-card"
            >
                <div
                    class="border-b p-6"
                >
                    <h2
                        class="font-semibold"
                    >
                        Related Information
                    </h2>
                </div>

                <div
                    class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- Status -->
                    <div
                        class="space-y-2"
                    >
                        <label
                            class="text-sm font-medium"
                        >
                            Status
                        </label>

                        <select
                            v-model="form.status"
                            class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option
                                v-for="status in statusOptions"
                                :key="status.value"
                                :value="status.value"
                            >
                                {{ status.label }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.status"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div
                class="flex justify-end gap-4 p-6"
            >
                <Button
                    type="button"
                    variant="outline"
                    as-child
                >
                    <Link href="/po">
                        Cancel
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                >
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