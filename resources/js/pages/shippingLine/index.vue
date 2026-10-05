<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue'

const search = ref('')

const props = defineProps<{
    shippingLines?: ShippingLine[]
}>()

const filteredShippingLine = computed(() => {
    const shippingLines = props.shippingLines ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return shippingLines
    }

    return shippingLines.filter((shippingLine: ShippingLine) => {
        return [
            shippingLine.shipping_line_name,
            shippingLine.contact_person,
            shippingLine.email,
            shippingLine.phone,
            shippingLine.website,
            shippingLine.is_active,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})
type ShippingLine = {
    id?: number | string
    shipping_line_name?: string | null
    contact_person?: string | null
    email?: string | null
    phone?: string | null
    website?: string | null
    is_active?: boolean | number | string | null
}
</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Shipping Lines
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor shipping lines.
                </p>
            </div>

            <Button as-child>
                <Link href="/shippingLine/create">
                    Add Shipping Line
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search shipping lines..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your shipping lines.
                </TableCaption>

                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Shipping Line Name</TableHead>
                        <TableHead>Contact Person</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Website</TableHead>
                        <TableHead>Is Active</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="filteredShippingLine.length > 0">
                        <TableRow v-for="(shippingLines, index) in filteredShippingLine"
                            :key="shippingLines.id ?? index">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ shippingLines.shipping_line_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shippingLines.contact_person || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shippingLines.email || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shippingLines.phone || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ shippingLines.website || '-' }}
                            </TableCell>

                            <TableCell>
                                <Checkbox :model-value="shippingLines.is_active" disabled/>
                            </TableCell>

                            <TableCell class="text-right">
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
                                            <Link :href="`/shippingLine/${shippingLines.id}`">
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/shippingLine/${shippingLines.id}/edit`">
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableRow v-else>
                        <TableCell :colspan="9" class="h-32 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <p class="font-medium text-muted-foreground">
                                    {{ search.trim() ? 'No shipping lines match your search criteria.' : 'No shipping lines found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no shipping lines to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="text-sm text-muted-foreground">
            Showing {{ filteredShippingLine.length }}
            shipping lines.
        </div>
    </div>
</template>

<style></style>
