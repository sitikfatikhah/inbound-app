<script setup lang="ts">
import { index, store } from '@/actions/App/Http/Controllers/TrackingStatusController';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Section } from 'lucide-vue-next';

const form = useForm({
    category: '',
    code: '',
    name: '',
    is_active: true,
});

const handleSubmit = () => {
    form.post('/tracking-status', {
        onSuccess: () => {
            console.log('Product berhasil disimpan');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
}

</script>

<template>
    <div class="w-full max-w-6xl space-y-6 px-4">
        <Head title="Create Status"></Head>
    <main class="mx-auto w-full space-y-2 p-2 sm:p-8">
        <header>
            <Link :href="index.url()" class="inline-flex item-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4"/>Back to Index Status
            </Link>
           <div class="space-y-2">
                <h1 class="text-2xl pt-4 pl-2 font-semibold">Create Status</h1>
                <p class="tet-sm pl-2 text-muted-foreground">Create your status</p>
            </div> 
        </header>
        <!-- Form -->
        <section class="overflow-x-auto rounded-md border p-4 ">
            <Form @submit="handleSubmit" class="gap-4 space-y-6">
                <div class="grid grid-cols-2 gap-4 rounded-lg border bg-card p-4">
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Category</label>
                        <Input v-model="form.category"/>
                         <p v-if="form.errors.category" class="text-sm text-red-500">
                            {{ form.errors.category }}
                        </p>                
                    </div>
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Code</label>
                        <Input v-model="form.code"/>
                         <p v-if="form.errors.code" class="text-sm text-red-500">
                            {{ form.errors.code }}
                        </p>                    
                    </div>
                    <div class="space-y-2">
                        <Label for="form" class="text-sm font-medium">Name</label>
                        <Input v-model="form.name"/>
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
                            class="h-4 w-4 rounded border-input"
                        />
                    </div>
                    <div class="gap-4 p-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href=index.url()>Cancel</Link>
                        </Button>
                    </div>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'saving..' : 'Save Status'}}
                    </Button>
                </div>
            </Form>
        </Section>
    </main>
    </div>
    
</template>
