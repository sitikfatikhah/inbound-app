```vue
<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Users as UsersIcon } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';

import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
}

const props = defineProps<{
    users?: User[];
}>();

const search = ref('');

const userList = computed(() => props.users ?? []);

const filteredUsers = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return userList.value;
    }

    return userList.value.filter((user) => {
        return [user.name, user.email, user.email_verified_at].some((value) =>
            String(value ?? '')
                .toLowerCase()
                .includes(keyword),
        );
    });
});
</script>

<template>
    <div class="w-full space-y-6 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <UsersIcon class="h-6 w-6" />

                    <h1 class="text-2xl font-semibold">Users</h1>
                </div>

                <p class="text-sm text-muted-foreground">
                    Manage and monitor users.
                </p>
            </div>

            <Button as-child>
                <Link href="/users/create"> Add User </Link>
            </Button>
        </div>

        <!-- Search -->
        <div>
            <Input
                v-model="search"
                type="search"
                placeholder="Search users..."
                class="max-w-sm"
            />
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableCaption> A list of your users. </TableCaption>

                <!-- TABLE HEADER -->
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[70px]"> No. </TableHead>

                        <TableHead> Name </TableHead>

                        <TableHead> Email </TableHead>

                        <TableHead> Email Verified At </TableHead>

                        <TableHead class="w-[80px] text-right">
                            Actions
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <!-- ADA DATA -->
                    <template v-if="filteredUsers.length > 0">
                        <TableRow
                            v-for="(user, index) in filteredUsers"
                            :key="user.id"
                        >
                            <!-- No -->
                            <TableCell>
                                {{ index + 1 }}
                            </TableCell>

                            <TableCell class="font-medium">
                                {{ user.name }}
                            </TableCell>

                            <TableCell>
                                {{ user.email }}
                            </TableCell>

                            <TableCell>
                                {{ user.email_verified_at || '-' }}
                            </TableCell>

                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>

                                        <DropdownMenuSeparator />

                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/users/${user.id}`"
                                                class="w-full"
                                            >
                                                View
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/users/${user.id}/edit`"
                                                class="w-full"
                                            >
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/users/${user.id}`"
                                                method="delete"
                                                as="button"
                                                class="w-full text-left text-destructive"
                                            >
                                                Delete
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </template>

                    <!-- DATA KOSONG -->
                    <TableRow v-else>
                        <TableCell :colspan="5" class="h-32 text-center">
                            <div
                                class="flex flex-col items-center justify-center gap-2"
                            >
                                <UsersIcon
                                    class="h-8 w-8 text-muted-foreground"
                                />

                                <p class="font-medium text-muted-foreground">
                                    No users found.
                                </p>

                                <p class="text-sm text-muted-foreground">
                                    There are currently no users to display.
                                </p>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Result -->
        <div class="text-sm text-muted-foreground">
            Showing {{ filteredUsers.length }} users.
        </div>
    </div>
</template>
```
