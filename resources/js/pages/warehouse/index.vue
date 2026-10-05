<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const search = ref('')
const props = defineProps<{
    warehouses?: Warehouses[]
}>()

const filteredWarehouses = computed(() => {
    const Warehouses = props.warehouses ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return Warehouses
    }

    return Warehouses.filter((warehouses) => {
        return [
            warehouses.warehouse_code,
            warehouses.warehouse_name,
            warehouses.is_active,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})

type Warehouses = {
    id: number
    warehouse_code?: string
    warehouse_name?: string
    address?: string
    contact_person?: string
    email?: string
    phone?: string
    is_active?: boolean
}

</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Warehouses
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor warehouses.
                </p>
            </div>

            <Button as-child>
                <Link href="/warehouses/create">
                    Add Warehouse
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search warehouses..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your warehouses.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Warehouse Code</TableHead>
                        <TableHead>Warehouse Name</TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead>Contact Person</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Is Active</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredWarehouses.length > 0">

                        <TableRow v-for="(Warehouses, index) in filteredWarehouses" :key="Warehouses.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>
                            <TableCell>
                                {{ Warehouses.warehouse_code || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Warehouses.warehouse_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Warehouses.address || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Warehouses.contact_person || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Warehouses.email || '-' }}
                            </TableCell>
                            <TableCell>
                                {{ Warehouses.phone || '-' }}
                            </TableCell>
                            <TableCell>
                                <Checkbox :model-value="Warehouses.is_active" disabled />
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
                                            <Link :href="`/warehouses/${Warehouses.id}`">
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/warehouses/${Warehouses.id}/edit`">
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
                                    {{ search.trim() ? 'No warehouses match your search criteria.' : 'No warehouses found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no warehouses to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredWarehouses.length }}
            warehouses.
        </div>

    </div>
</template>

<style></style>