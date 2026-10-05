<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Link} from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Ports {
    id: number
    port_name: string
    country: string
    port_code: string
    city: string | undefined
    type: string | undefined
    is_active: boolean
}

const props = defineProps<{
    port: Ports
}>()
</script>
<template>
    <div class="w-full max-w-6xl space-y-6 px-4">

        <!-- Header -->
        <div>
            <Link href="/ports"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Ports
            </Link>
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                        View Port
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        View port details.
                    </p>
                </div>
                <!-- Edit -->
                <Button as-child>
                    <Link :href="`/ports/${props.port.id}/edit`">
                        Edit
                    </Link>
                </Button>
            </div>


        </div>

        <!-- Form -->
        <Form class="space-y-6">

            <!-- Port Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Port Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic port information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- port_id -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Port ID</label>
                        <Input v-model="props.port.id" placeholder="Enter port ID" disabled />
                    </div>
                    <!-- port_name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Port Name</label>
                        <Input v-model="props.port.port_name" placeholder="Enter port name" disabled />
                    </div>
                    <!-- country -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country</label>
                        <Input v-model="props.port.country" placeholder="Enter country" disabled />
                    </div>

                    <!-- port_code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Port Code</label>
                        <Input v-model="props.port.port_code" placeholder="Enter port code" disabled />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">City</label>
                        <Input v-model="props.port.city" placeholder="Enter city" disabled />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Type</label>
                        <Input v-model="props.port.type" placeholder="Enter port type" disabled />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="props.port.is_active" disabled />
                    </div>
                </div>
            </div>
        </Form>
    </div>
</template>

<style></style>