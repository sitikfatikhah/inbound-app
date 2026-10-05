<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Products {
    id: number
    product_code: string
    product_name: string
    hs_code: string
    import_duty: number | string
    facility: string
    description: string
    brand: string
    category: string
    unit: string
    country_of_origin: string
    is_active: boolean
}
const props = defineProps<{
    product: Products
}>()

</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/products"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Products
            </Link>

            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                        Product Details
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        View the details of the selected product.
                    </p>
                </div>
                <!-- Edit -->
                <Button as-child>
                    <Link :href="`/products/${props.product.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>

        </div>

        <!-- Form -->
        <Form class="space-y-6">

            <!-- products InFormation -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Product Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic product information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Product ID -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Product ID</label>
                        <Input v-model="props.product.id" placeholder="Enter product ID" readonly />
                    </div>
                    <!-- Product Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Product Code</label>
                        <Input v-model="props.product.product_code" placeholder="Enter product code" readonly />
                    </div>

                    <!-- Product Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Product Name</label>
                        <Input v-model="props.product.product_name" placeholder="Enter product name" readonly />
                    </div>

                    <!-- Hs Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Hs Code</label>
                        <Input v-model="props.product.hs_code" placeholder="Enter HS code" readonly />
                    </div>
                    <!-- Import Duty -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Import Duty</label>
                        <Input v-model="props.product.import_duty" placeholder="Enter import duty" readonly />
                    </div>
                    <!-- Facility -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Facility</label>
                        <Input v-model="props.product.facility" placeholder="Enter facility" readonly />
                    </div>
                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Description</label>
                        <Input v-model="props.product.description" placeholder="Enter product description" readonly />
                    </div>
                    <!-- Brand -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Brand</label>
                        <Input v-model="props.product.brand" placeholder="Enter brand" readonly />
                    </div>
                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Category</label>
                        <Input v-model="props.product.category" placeholder="Enter category" readonly />
                    </div>
                    <!-- Unit -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Unit</label>
                        <Input v-model="props.product.unit" placeholder="Enter unit" readonly />
                    </div>
                    <!-- Country of Origin -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country of Origin</label>
                        <Input v-model="props.product.country_of_origin" placeholder="Enter country of origin"
                            readonly />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="props.product.is_active" disabled />
                    </div>
                </div>
            </div>

        </Form>
    </div>
</template>

<style></style>