<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'

interface Forwarders {
    id: number
    forwarder_name: string | null
    contact_person: string | null
    email: string | null
    phone: string | null
    is_active: boolean | string | number | null
}

const props = defineProps<{
    forwarder: Forwarders
}>()
</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/forwarders"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Forwarders
            </Link>

            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        View Forwarder
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        View the freight forwarder information.
                    </p>
                </div>

                <!-- Edit -->
                <Button as-child>
                    <Link :href="`/forwarders/${forwarder.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Forwarder Information -->
        <div class="rounded-lg border bg-card">

            <div class="border-b p-6">
                <h2 class="font-semibold">
                    Forwarder Information
                </h2>

                <p class="text-sm text-muted-foreground">
                    Freight forwarder information from database.
                </p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                <!-- ID -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        ID
                    </label>

                    <Input :model-value="String(props.forwarder.id)" disabled />
                </div>

                <!-- Forwarder Name -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        Forwarder Name
                    </label>

                    <Input :model-value="props.forwarder.forwarder_name ?? '-'" disabled />
                </div>

                <!-- Contact Person -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        Contact Person
                    </label>

                    <Input :model-value="props.forwarder.contact_person ?? '-'" disabled />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        Email
                    </label>

                    <Input :model-value="props.forwarder.email ?? '-'" disabled />
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        Phone Number
                    </label>

                    <Input :model-value="props.forwarder.phone ?? '-'" disabled />
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">
                        Is Active
                    </label>

                    <div class="flex h-10 items-center gap-2">
                        <Checkbox :model-value="Boolean(props.forwarder.is_active)" disabled />

                        <span class="text-sm">
                            {{
                                Boolean(props.forwarder.is_active)
                                    ? 'Active'
                                    : 'Inactive'
                            }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped></style>
