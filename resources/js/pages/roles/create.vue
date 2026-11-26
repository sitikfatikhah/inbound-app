<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'

interface Permission {
    id: number
    name: string
}

const props = defineProps<{
    permissions: Permission[]
}>()

const form = useForm({
    name: '',
    permissions: [] as string[],
})

function togglePermission(permission: string, checked: boolean) {
    if (checked) {
        if (!form.permissions.includes(permission)) {
            form.permissions.push(permission)
        }
    } else {
        form.permissions = form.permissions.filter(
            item => item !== permission
        )
    }
}

function submit() {
    form.post('/roles')
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold">Create Role</h1>
            <p class="text-muted-foreground">
                Buat role dan tentukan permission yang dimiliki.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
                <Label for="name">Role Name</Label>

                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="Contoh: Document Controller"
                />

                <p v-if="form.errors.name" class="text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="space-y-3">
                <h2 class="font-semibold">Permissions</h2>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="permission in props.permissions"
                        :key="permission.id"
                        class="flex items-center gap-2 rounded-md border p-3"
                    >
                        <Checkbox
                            :id="`permission-${permission.id}`"
                            :checked="form.permissions.includes(permission.name)"
                            @update:checked="
                                (checked: boolean) => togglePermission(
                                    permission.name,
                                    Boolean(checked)
                                )
                            "
                        />

                        <Label :for="`permission-${permission.id}`">
                            {{ permission.name }}
                        </Label>
                    </div>
                </div>

                <p
                    v-if="form.errors.permissions"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.permissions }}
                </p>
            </div>

            <div class="flex gap-2">
                <Button type="submit" :disabled="form.processing">
                    Save Role
                </Button>

                <Button
                    type="button"
                    variant="outline"
                    @click="$inertia.visit('/roles')"
                >
                    Cancel
                </Button>
            </div>
        </form>
    </div>
</template>
