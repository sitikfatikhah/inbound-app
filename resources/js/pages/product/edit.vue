<script setup lang="ts">
import { Form, Link, useForm } from '@inertiajs/vue3';
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

const form = useForm({
    product_code: props.product.product_code,
    product_name: props.product.product_name,
    hs_code: props.product.hs_code,
    import_duty: props.product.import_duty,
    facility: props.product.facility,
    description: props.product.description,
    brand: props.product.brand,
    category: props.product.category,
    unit: props.product.unit,
    country_of_origin: props.product.country_of_origin,
    is_active: props.product.is_active as boolean,
})
const handleSubmit = () => {
    form.put(`/products/${props.product.id}`, {
        onSuccess: () => {
            console.log('Product berhasil diperbarui');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
}

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
                        Edit the details of the selected product.
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
        <Form @submit.prevent="handleSubmit" class="space-y-6">

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

                    <!-- Product Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Product Code</label>
                        <Input v-model="form.product_code" placeholder="Enter product code" />
                    </div>

                    <!-- Product Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Product Name</label>
                        <Input v-model="form.product_name" placeholder="Enter product name" />
                    </div>

                    <!-- Hs Code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Hs Code</label>
                        <Input v-model="form.hs_code" placeholder="Enter HS code" />
                    </div>
                    <!-- Import Duty -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Import Duty</label>
                        <Input v-model="form.import_duty" placeholder="Enter import duty" />
                    </div>
                    <!-- Facility -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Facility</label>
                        <Input v-model="form.facility" placeholder="Enter facility" />
                    </div>
                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Description</label>
                        <Input v-model="form.description" placeholder="Enter product description" />
                    </div>
                    <!-- Brand -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Brand</label>
                        <Input v-model="form.brand" placeholder="Enter brand" />
                    </div>
                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Category</label>
                        <Input v-model="form.category" placeholder="Enter category" />
                    </div>
                    <!-- Unit -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Unit</label>
                        <Input v-model="form.unit" placeholder="Enter unit" />
                    </div>
                    <!-- Country of Origin -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country of Origin</label>
                        <Input v-model="form.country_of_origin" placeholder="Enter country of origin"  />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="form.is_active" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4 p-6">
                <Button type="button" variant="outline" as-child>
                    <Link href="/products">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Product' }}
                </Button>
            </div>

        </Form>
    </div>
</template>

<style></style>