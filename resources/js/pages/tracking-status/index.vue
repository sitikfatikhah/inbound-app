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
    tracking_statuses?: string;
    category: string;
    code: string;
    name: string;
    is_active: boolean;
}

const props = defineProps<{ tracking_statuses: TrackingStatus[] }>();
const search = ref('');
const filteredStatus = computed(() => {
    const keyword = search.value.trim().toLocaleLowerCase();

    if (!keyword) {
        return props.tracking_statuses;
    }

    return props.tracking_statuses.filter((tracking_statuses) =>
        [
            tracking_statuses.tracking_statuses,
            tracking_statuses.category,
            tracking_statuses.code,
            tracking_statuses.name,
            tracking_statuses.is_active,
        ].some((value) => String(value ?? '').toLocaleLowerCase().includes(keyword)),
    );
});

</script>

<template>
    <div>
        <Head title="Status" />

        <main class="mx-auto w-full space-y-6 p-4 sm:p-8">
            <!-- Header -->
            <header class="space-y-4">
                <div>
                    <h1 class="pl-2 pt-4 text-2xl font-semibold tracking-tight">
                        Status
                    </h1>

                    <p class="pl-2 text-sm text-muted-foreground">
                        Manage your status settings.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Search status..."
                        class="w-full sm:w-80"
                    />

                    <Button as-child>
                        <Link :href="create.url()">
                            Add a Status
                        </Link>
                    </Button>
                </div>
            </header>

            <!-- Table -->
            <section class="space-y-3">
                <div class="overflow-x-auto rounded-lg border">
                    <Table>
                        <TableCaption class="pb-4">
                            A list of your statuses.
                        </TableCaption>

                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-16 text-center">
                                    ID
                                </TableHead>

                                <TableHead class="min-w-32">
                                    Category
                                </TableHead>

                                <TableHead class="min-w-28">
                                    Code
                                </TableHead>

                                <TableHead class="min-w-28">
                                    Name
                                </TableHead>

                                <TableHead class="min-w-28 text-center">
                                    Is Active
                                </TableHead>

                                <TableHead class="w-30 text-centre">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <template v-if="filteredStatus.length">
                                <TableRow
                                    v-for="tracking_statuses in filteredStatus"
                                    :key="tracking_statuses.id"
                                    class="hover:bg-muted/30"
                                >
                                    <TableCell class="text-center font-medium">
                                        {{ tracking_statuses.id }}
                                    </TableCell>

                                    <TableCell>
                                        {{ tracking_statuses.category }}
                                    </TableCell>

                                    <TableCell class="font-mono text-sm">
                                        {{ tracking_statuses.code }}
                                    </TableCell>

                                    <TableCell class="font-medium">
                                        {{ tracking_statuses.name }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <span
                                            :class="
                                                tracking_statuses.is_active
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            "
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                        >
                                            {{ tracking_statuses.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </TableCell>

                                    <TableCell class="text-centre">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="h-8 w-8"
                                                >
                                                    <span class="text-lg">⋮</span>
                                                </Button>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel>
                                                    Actions
                                                </DropdownMenuLabel>

                                                <DropdownMenuSeparator />

                                                <DropdownMenuItem as-child>
                                                    <Link :href="`/tracking-status/${tracking_statuses.id}`">
                                                        View
                                                    </Link>
                                                </DropdownMenuItem>

                                                <DropdownMenuItem as-child>
                                                    <Link :href="`/tracking-status/${tracking_statuses.id}/edit`">
                                                        Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            </template>

                            <TableRow v-else>
                                <TableCell
                                    colspan="7"
                                    class="h-32 text-center text-muted-foreground"
                                >
                                    {{
                                        search.trim()
                                            ? 'No status matches your search criteria.'
                                            : 'No status found.'
                                    }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between px-2">
                    <p class="text-sm text-muted-foreground">
                        Showing
                        <span class="font-medium text-foreground">
                            {{ filteredStatus.length }}
                        </span>
                        statuses.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
