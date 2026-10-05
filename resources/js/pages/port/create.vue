<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    port_name: '',
    country: '',
    port_code: '',
    city: '',
    type: '',
    is_active: false as boolean,
})
const handleSubmit = () => {
    form.post('/ports', {
        onSuccess: () => {
            console.log('Port berhasil disimpan');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
}
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

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Port
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new port.
            </p>
        </div>

        <!-- Form -->
        <Form @submit.prevent="handleSubmit" class="space-y-6">

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
                    
                    <!-- port_name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Port Name</label>
                        <Input v-model="form.port_name" placeholder="Enter port name" />
                    </div>
                    <!-- country -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Country</label>
                        <Input v-model="form.country" placeholder="Enter country" />
                    </div>

                    <!-- port_code -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Port Code</label>
                        <Input v-model="form.port_code" placeholder="Enter port code" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">City</label>
                        <Input v-model="form.city" placeholder="Enter city" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Type</label>
                        <Input v-model="form.type" placeholder="Enter port type" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label class="text-sm font-medium" for="toggle">Is Active</Label>
                        <Checkbox id="toggle" v-model="form.is_active" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4 p-6">
                <Button type="button" variant="outline" as-child>
                    <Link href="/ports">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Port' }}
                </Button>
            </div>
        </Form>
    </div>
</template>

<style>

</style>