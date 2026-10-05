<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    Bell,
    Search,
} from 'lucide-vue-next';

const searchQuery = ref('');
const currentUser = ref<{ name?: string } | null>(null);

const userName = computed(() => currentUser.value?.name ?? 'Pengguna');
const userInitial = computed(() => userName.value.charAt(0).toUpperCase());

function handleSearch() {
    const query = searchQuery.value.trim();

    if (!query) {
        return;
    }

    // Siapkan integrasi pencarian global di tahap berikutnya.
    console.log('Global search:', query);
}
</script>

<template>
    <!-- =========================================================
             TOP NAVIGATION
        ========================================================== -->
    <header
        class="fixed left-72 right-0 top-0 z-40 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-8 backdrop-blur-md">
        <!-- Search -->
        <form class="w-full max-w-xl" @submit.prevent="handleSearch">
            <div class="group relative">
                <Search
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-[#00355f]" />

                <input v-model="searchQuery" type="search" placeholder="Cari shipment, PO, produk, atau vendor..."
                    class="h-10 w-full rounded-full border-0 bg-slate-100 pl-10 pr-4 text-sm text-slate-800 outline-none ring-0 transition-all placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-[#00355f]/15" />
            </div>
        </form>

        <!-- Right Navigation -->
        <div class="ml-6 flex shrink-0 items-center gap-5">
            <!-- Connection Status -->
            <div
                class="hidden items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 xl:flex">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />

                <span class="text-xs font-semibold text-emerald-700">
                    Dynamics 365 Terhubung
                </span>
            </div>

            <!-- Notifications -->
            <button type="button" aria-label="Notifikasi"
                class="relative rounded-full p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-[#00355f]">
                <Bell class="h-5 w-5" />

                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white" />
            </button>

            <div class="h-8 w-px bg-slate-200" />

            <!-- User -->
            <div class="flex items-center gap-3">
                <div class="hidden text-right lg:block">
                    <p class="max-w-40 truncate text-sm font-semibold text-slate-800">
                        {{ userName }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Supply Chain
                    </p>
                </div>

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-100 bg-blue-50 text-sm font-bold text-[#00355f]">
                    {{ userInitial }}
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped></style>