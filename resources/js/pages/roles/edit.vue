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

interface Role {
    id: number
    name: string
    permissions: Permission[]
}

const props = defineProps<{
    role: Role
    permissions: Permission[]
}>()

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map(
        permission => permission.name
    ),
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
    form.put(`/roles/${props.role.id}`)
}
</script>

<template>
    <div class="space-y-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold">Edit Role</h1>
            <p class="text-muted-foreground">
                Ubah nama role dan permission.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
                <Label for="name">Role Name</Label>

                <Input id="name" v-model="form.name" />

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
                                checked => togglePermission(
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
            </div>

            <Button type="submit" :disabled="form.processing">
                Update Role
            </Button>
        </form>
    </div>
</template>
