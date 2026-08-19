<script setup lang="ts">
defineProps<{
    reference: string
}>()
</script>

<template>
    <div>
        <h1>BL Number Search</h1>

        <p>
            Hasil pencarian:
            <strong>{{ reference }}</strong>
        </p>
    </div>
</template>