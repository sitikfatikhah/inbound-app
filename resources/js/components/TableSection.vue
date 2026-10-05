<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, ClipboardList } from 'lucide-vue-next';

interface ExceptionItem { id: number; reference: string; detail: string; product: string; vendor: string; status: string; eta: string | null; action: string }
const props = defineProps<{ exceptions: ExceptionItem[] }>();
function formatDate(value: string | null) { return value ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(value)) : '-'; }
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <header class="flex flex-col gap-3 border-b border-border p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-destructive/10 p-2.5 text-destructive">
                    <ClipboardList class="size-5" />
                </div>
                <div>
                    <h2 class="text-lg font-bold">Aksi diperlukan</h2>
                    <p class="text-xs text-muted-foreground">Keterlambatan dan clearance yang perlu ditangani</p>
                </div>
            </div>
            <Link href="/tracking/import"
                class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">Lihat
                monitoring
                <ArrowUpRight class="size-4" />
            </Link>
        </header>
        <div v-if="props.exceptions.length" class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead class="bg-muted/50 text-[11px] uppercase text-muted-foreground">
                    <tr>
                        <th class="px-6 py-4">Shipment</th>
                        <th class="px-4 py-4">Produk</th>
                        <th class="px-4 py-4">Vendor</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">ETA</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="item in props.exceptions" :key="`${item.id}-${item.status}`"
                        class="transition hover:bg-muted/30">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold">{{ item.reference }}</p>
                            <p class="text-xs text-muted-foreground">{{ item.detail }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm">{{ item.product }}</td>
                        <td class="px-4 py-4 text-sm">{{ item.vendor }}</td>
                        <td class="px-4 py-4"><span
                                class="rounded-full bg-destructive/10 px-3 py-1 text-[11px] font-bold text-destructive">{{
                                item.status }}</span></td>
                        <td class="px-4 py-4 text-sm text-muted-foreground">{{ formatDate(item.eta) }}</td>
                        <td class="px-6 py-4 text-right">
                            <Link :href="`/shipments/${item.id}`"
                                class="rounded-lg border border-primary px-3 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-primary-foreground">
                                {{ item.action }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="p-10 text-center">
            <p class="font-semibold">Semua operasional terkendali</p>
            <p class="mt-1 text-sm text-muted-foreground">Belum ada keterlambatan atau clearance tertunda.</p>
        </div>
    </section>
</template>
