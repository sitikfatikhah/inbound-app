<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Section } from 'lucide-vue-next';
import status from '@/routes/tracking-status';
import { index } from '@/actions/App/Http/Controllers/TrackingStatusController';

interface TrackingStatus {
    id: number;
    category: string;
    code: string;
    name: string;
    is_active: boolean;
}

const props = defineProps<{
    tracking_statuses: TrackingStatus;
}>();

const form = useForm({
    category: props.tracking_statuses.category,
    code: props.tracking_statuses.code,
    name: props.tracking_statuses.name,
    is_active: props.tracking_statuses.is_active as boolean,
})

const handleSubmit = () => {
    form.put('/tracking-status/${props.tracking_statuses.id}', {
        onSuccess: () => {
            console.log('Tracking Status berhasil disimpan');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
}

</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <Head title="View Status"></Head>
    <main class="mx-auto w-full space-y-2 p-2 sm:p-8">
        <header>
            <Link :href="index.url()" class="inline-flex item-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4"/>Back to Index Status
            </Link>
           <div class="space-y-2">
                <h1 class="text-2xl pt-4 pl-2 font-semibold">View Status</h1>
                <p class="tet-sm pl-2 text-muted-foreground">View your status</p>
            </div> 
        </header>
        <!-- Form -->
        <section class="overflow-x-auto rounded-md border p-4 ">
            <Form @submit="handleSubmit" class="gap-4 space-y-6">
                <div class="grid grid-cols-2 gap-4 rounded-lg border bg-card p-4">
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Category</label>
                        <Input v-model="form.category" readonly/>
                         <p v-if="form.errors.category" class="text-sm text-red-500">
                            {{ form.errors.category }}
                        </p>                
                    </div>
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Code</label>
                        <Input v-model="form.code" readonly/>
                         <p v-if="form.errors.code" class="text-sm text-red-500">
                            {{ form.errors.code }}
                        </p>                    
                    </div>
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Name</label>
                        <Input v-model="form.name" readonly/>
                        <p v-if="form.errors.name" class="text-sm text-red-500">
                            {{ form.errors.name }}
                        </p>                   
                    </div>
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Active</Label>
                        <input
                            id="form"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input" readonly
                        />
                    </div>
                    
                </div>
                    <div class="flex justify-end gap-4 p-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href=index.url()>Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'saving..' : 'Save Status'}}
                        </Button>
                    </div>
            </Form>
        </Section>
    </main>
    </div>
    
</template>
