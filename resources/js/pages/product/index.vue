<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';

const search = ref('')

const props = defineProps<{
    product?: Products[]
}>()

type Products = {
    id: number
    product_code?: string
    product_name?: string
    hs_code?: string
    import_duty?: number | string
    facility?: string
    description?: string
    brand?: string
    category?: string
    unit?: string
    country_of_origin?: string
    is_active?: boolean
}

const filteredProducts = computed(() => {
    const product = props.product ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return product
    }

    return product.filter((product) => {
        return [
            product.product_code,
            product.product_name,
            product.hs_code,
            product.import_duty,
            product.facility,
            product.description,
            product.brand,
            product.category,
            product.unit,
            product.country_of_origin,
            product.is_active,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})
</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Products
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor products.
                </p>
            </div>

            <Button as-child>
                <Link href="/products/create">
                Add Product
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search products..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your products.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Product Code</TableHead>
                        <TableHead>Product Name</TableHead>
                        <TableHead>HS Code</TableHead>
                        <TableHead>Import Duty</TableHead>
                        <TableHead>Facility</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Brand</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Unit</TableHead>
                        <TableHead>Country of Origin</TableHead>
                        <TableHead>Is Active</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredProducts.length > 0">

                        <TableRow v-for="(Products, index) in filteredProducts" :key="Products.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ Products.product_code || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Products.product_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Products.hs_code || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Products.import_duty || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.facility || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.description || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.brand || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.category || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.unit || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Products.country_of_origin || '-' }}
                            </TableCell>

                            <TableCell>
                                <Checkbox :model-value="Products.is_active" disabled />
                            </TableCell>

                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            ⋮
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>

                                        <DropdownMenuSeparator />

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/products/${Products.id}`">
                                            View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/products/${Products.id}/edit`">
                                            Edit
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>

                    </template>

                    <!-- DATA KOSONG -->
                    <TableRow v-else>
                        <TableCell :colspan="10" class="h-32 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <p class="font-medium text-muted-foreground">
                                    {{ search.trim() ? 'No products match your search criteria.' : 'No products found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no products to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredProducts.length }}
            products.
        </div>

    </div>
</template>

<style>
</style>