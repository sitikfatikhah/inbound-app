<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,} from '@/components/ui/dropdown-menu'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue'


const search = ref('')
const props = defineProps<{
    ports?: Ports[]
}>()

const filteredPorts = computed(() => {
    const ports = props.ports ?? []
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return ports
    }

    return ports.filter((ports: Ports) => {
        return [
            ports.country,
            ports.port_code,
            ports.port_name,
            ports.city,
            ports.type,
            ports.is_active,
        ].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword)
        )
    })
})

type Ports = {
    id: number
    country?: string | number
    port_code?: string
    port_name?: string
    city?: string
    type?: string
    is_active?: boolean
}
</script>

<template>
    <div class="w-full space-y-6 p-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Ports
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor ports.
                </p>
            </div>

            <Button as-child>
                <Link href="/ports/create">
                    Add Port
                </Link>
            </Button>
        </div>
               <!-- Search -->
        <div>
            <Input v-model="search" type="search" placeholder="Search ports..." class="max-w-sm" />
        </div>


        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption>
                    A list of your ports.
                </TableCaption>

                <!-- HEADER SELALU TAMPIL -->
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Country</TableHead>
                        <TableHead>Port Code</TableHead>
                        <TableHead>Port Name</TableHead>
                        <TableHead>City</TableHead>
                        <TableHead>Type</TableHead>
                        <TableHead>Is Active</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>

                    <!-- ADA DATA -->
                    <template v-if="filteredPorts.length > 0">

                        <TableRow v-for="(Ports, index) in filteredPorts" :key="Ports.id">
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell>
                                {{ Ports.country || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Ports.port_code || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Ports.port_name || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Ports.city || '-' }}
                            </TableCell>

                            <TableCell>
                                {{ Ports.type || '-' }}
                            </TableCell>

                            <TableCell>
                                <Checkbox :model-value="Ports.is_active" disabled />
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
                                            <Link :href="`/ports/${Ports.id}`">
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link :href="`/ports/${Ports.id ?? null}/edit`">
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
                                    {{ search.trim() ? 'No ports match your search criteria.' : 'No ports found.' }}
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    {{ search.trim() ? 'Try a different search term.' : 'There are currently no ports to display.' }}
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>

                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredPorts.length }}
            ports.
        </div>

    </div>
</template>

<style></style>