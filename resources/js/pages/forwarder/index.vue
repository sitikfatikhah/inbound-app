<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow,} from '@/components/ui/table'
import {DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,} from '@/components/ui/dropdown-menu'
import { Checkbox } from '@/components/ui/checkbox'

const search = ref('')
type Forwarder = {
    id: number
    forwarder_name?: string
    contact_person?: string | null
    email?: string | null
    phone?: string | null
    is_active?: boolean
}

const props = defineProps<{
    forwarders?: Forwarder[]
}>()
const filteredforwarders = computed(() => {
    const Forwarders = props.forwarders ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return Forwarders
    }

    return Forwarders.filter((forwarders: Forwarder) => {
        return [
            forwarders.forwarder_name,
            forwarders.contact_person,
            forwarders.email,
            forwarders.phone,
            forwarders.is_active,

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
                    Freight Forwarders
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor freight forwarders.
                </p>
            </div>

            <Button as-child>
                <Link href="/forwarders/create">
                    Add Freight Forwarder
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search forwarders..." class="max-w-sm" />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your freight forwarders.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Forwarder Name</TableHead>
                        <TableHead>Contact Person</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Is Active</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredforwarders.length > 0">

                        <TableRow v-for="(forwarders, index) in filteredforwarders" :key="forwarders.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ forwarders.forwarder_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ forwarders.contact_person || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ forwarders.email || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ forwarders.phone || '-' }}
                            </TableCell>

                             <TableCell class="text-center">
                                <Checkbox 
                                :model-value="Boolean(forwarders.is_active)" disabled />
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
                                            <Link :href="`/forwarders/${forwarders.id}`">
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/forwarders/${forwarders.id}/edit`">
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
                                    {{ search.trim() ? 'No freight forwarders match your search criteria.' : 'No freight forwarders found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no freight forwarders to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredforwarders.length }}
            freight forwarders.
        </div>

    </div>
</template>
<style></style>