<script setup lang="ts">
import PublicSidebar from '@/components/public/PublicSidebar.vue'
</script>

<template>
    <div class="min-h-screen bg-background">
        <PublicSidebar />

        <main class="min-h-screen lg:pl-[var(--sidebar-width)]">
            <slot />
        </main>
    </div>
</template>