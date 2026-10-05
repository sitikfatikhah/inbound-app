<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue'

const search = ref('')

const props = defineProps<{
    supplier?: Suppliers[]
}>()
type Suppliers = {
    id?: number | string
    supplier_code?: string
    supplier_name?: string
    country?: string
    address?: string
    contact_person?: string
    email?: string
    phone?: string
    is_active?: boolean
}


const filteredsupplier = computed(() => {
    const Suppliers = props.supplier ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return Suppliers
    }

    return Suppliers.filter((supplier: Suppliers) => {
        return [
            supplier.id,
            supplier.supplier_code,
            supplier.supplier_name,
            supplier.country,
            supplier.address,
            supplier.contact_person,
            supplier.email,
            supplier.phone,
            supplier.is_active,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })

})
const checked = ref(false)
</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Suppliers
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor suppliers.
                </p>
            </div>

            <Button as-child>
                <Link href="/suppliers/create">
                    Add Supplier
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search suppliers..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your suppliers.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Supplier Code</TableHead>
                        <TableHead>Supplier Name</TableHead>
                        <TableHead>Country</TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead>Contact Person</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Is Active</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredsupplier.length > 0">

                        <TableRow v-for="(supplier, index) in filteredsupplier" :key="supplier.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ supplier.supplier_code || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ supplier.supplier_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ supplier.country || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ supplier.address || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ supplier.contact_person || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ supplier.email || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ supplier.phone || '-' }}
                            </TableCell>
                            <TableCell class="text-center">
                                <Checkbox :model-value="Boolean(supplier.is_active)" disabled />
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
                                            <Link :href="`/suppliers/${supplier.id}`">
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/suppliers/${supplier.id}/edit`">
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
                                    {{ search.trim() ? 'No suppliers match your search criteria.' : 'No suppliers found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no suppliers to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredsupplier.length }}
            suppliers.
        </div>

    </div>
</template>

<style></style>