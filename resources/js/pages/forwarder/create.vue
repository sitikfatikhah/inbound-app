<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const form = useForm({
    id: '',
    forwarder_name: '',
    contact_person: '',
    email: '',
    phone: '',
    is_active: false
})
const handleSubmit = () => {
    form.post('/forwarders', {
        onSuccess: () => {
            console.log('Forwarder berhasil ditambahkan');
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
            <Link href="/forwarders"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
            <ArrowLeft class="h-4 w-4" />
            Back to Forwarder
            </Link>

            <h1 class="mt-4 text-2xl font-semibold tracking-tight">
                Add Forwarder
            </h1>

            <p class="text-sm text-muted-foreground">
                Create a new freight forwarder.
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-6">

            <!-- Forwarder Information -->
            <div class="rounded-lg border bg-card">

                <div class="border-b p-6">
                    <h2 class="font-semibold">
                        Forwarder Information
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        Enter the basic freight forwarder information.
                    </p>
                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">

                    <!-- Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Name</label>
                        <Input v-model="form.forwarder_name" placeholder="Enter forwarder name" />
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Address</label>
                        <Input v-model="form.contact_person" placeholder="Enter forwarder PIC" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Phone Number</label>
                        <Input v-model="form.phone" placeholder="Enter phone number" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <Input v-model="form.email" placeholder="Enter email" />
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
                    <Link href="/forwarders">
                        Cancel
                    </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Freight Forwarder' }}
                </Button>
            </div>
        </form>
    </div>
</template>

<style></style>