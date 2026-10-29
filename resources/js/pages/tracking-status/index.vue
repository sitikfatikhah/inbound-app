<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { create } from '@/actions/App/Http/Controllers/TrackingStatusController';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';

interface TrackingStatus {
    id: number;
    tracking_status?: string;
    category: string;
    code: string;
    name: string;
    sort_order: number;
    is_active: boolean;
}

const props = defineProps<{ status: TrackingStatus[] }>();
const search = ref('');
const filteredStatus = computed(() => {
    const keyword = search.value.trim().toLocaleLowerCase();

    if (!keyword) {
        return props.status;
    }

    return props.status.filter((status) =>
        [
            status.tracking_status,
            status.category,
            status.code,
            status.name,
            status.sort_order,
            status.is_active,
        ].some((value) => String(value ?? '').toLocaleLowerCase().includes(keyword)),
    );
});

</script>

<template>
    <div>
        <Head title="Local Shipment"></Head>
        <main class="mx-auto w-full space-y-2 p-2 sm:p-8">
            <header>
                <div class="space-y-2">
                    <h1 class="text-2xl pt-4 pl-2 font-semibold">Tracking Status</h1>
                    <p class="tet-sm pl-2 text-muted-foreground">Setting your status with function Tracking Status</p>
                </div>
                <div class="flex justify-end">
                    <Button as-child>
                        <Link :href="create.url()">Add a status</Link>
                    </Button>
                </div>
                <Input v-model="search" type="search" placeholder="Search status" class="w-84 mb-4"/>
            </header>
            
        
    
            <section>
                <div class="overflow-x-auto rounded-md border">
                    <Table>
                    <TableCaption>A List of your status</TableCaption>
                    <TableHeader>
                        <TableRow class="pl-4">
                            <TableHead class="w-[100px]">ID</TableHead>
                            <TableHead class="w-[100px]">Category</TableHead>
                            <TableHead class="w-[100px]">Code</TableHead>
                            <TableHead class="w-[100px]">Name</TableHead>
                            <TableHead class="w-[100px]">Sort Order</TableHead>
                            <TableHead class="w-[100px]">Is Active</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="filteredStatus.length">
                            <TableRow v-for="status in filteredStatus" :key="status.id">
                            <TableCell class="-mediumfont">{{status.id}}</TableCell>
                            <TableCell class="-mediumfont">{{status.category}}</TableCell>
                            <TableCell>{{status.code}}</TableCell>
                            <TableCell>{{status.name}}</TableCell>
                            <TableCell>{{status.sort_order}}</TableCell>
                            <TableCell id="is_active">{{status.is_active}}</TableCell>

                        <TableCell>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child><Button variant="ghost" size="icon">⋮</Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem as-child>
                                                    <Link :href="`/status/${status.id}`">View</Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem as-child>
                                                    <Link :href="`/status/${status.id}/edit`">Edit</Link>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                        </TableRow>
                        </template>
                        <TableRow v-else>
                                <TableCell colspan="19" class="h-32 text-center">{{ search.trim() ? 'No status match your search criteria.' : 'No status found.' }}</TableCell>
                            </TableRow>
                    </TableBody>
                    </Table>
                </div>
                <div class="text-sm text-muted-foreground">Showing {{ filteredStatus.length }} Status.</div>
            </Section>
        </main>
    </div>

</template>