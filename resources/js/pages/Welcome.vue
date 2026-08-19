<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';
import LandingLayout from '@/layouts/LandingLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/Input.vue';
</script>

<template>
    <LandingLayout>
        <!-- Hero Section -->
        <section class="hero-gradient pt-24 pb-32 px-margin-desktop text-center relative overflow-hidden">
            <div class="max-w-4xl mx-auto relative z-10">
                <h1 class="font-display text-display text-primary mb-6">Pantau Rantai Pasok Anda dengan Presisi
                    Real-Time</h1>
                <p class="text-body-lg text-muted mb-12 max-w-2xl mx-auto">Monitor perjalanan logistik ujung-ke-ujung
                    Anda dari titik asal hingga tujuan akhir dengan mesin orkestrasi logistik canggih PT. CSA.</p>
                <div
                    class="bg-surface shadow-xl rounded-xl p-3 flex flex-col md:flex-row gap-3 max-w-3xl mx-auto border border-border">
                    
                    <div class="flex-grow relative">
                        <Input/>
                    </div>                    
                </div>
                <div class="mt-8 flex justify-center gap-6 text-label-md text-muted">
                    <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-success"></span> 1.248
                        Pengiriman Aktif</span>
                    <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-info"></span> 42 Kapal di
                        Laut</span>
                    <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-warning"></span> 12
                        Penyelesaian Pelabuhan Hari Ini</span>
                </div>
            </div>
        </section>
        <!-- Arrival Schedule Section -->
        <section class="py-xl px-margin-desktop max-w-container-max mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
                <div>
                    <h2 class="font-display text-headline-lg text-primary mb-2">Jadwal Kedatangan Shipment</h2>
                    <p class="text-body-md text-muted">Pemantauan kedatangan mingguan terintegrasi untuk semua saluran
                        logistik.</p>
                </div>
                <div class="flex p-1 bg-surface-container-low rounded-lg border border-border">
                    <button
                        class="px-6 py-2 bg-white shadow-sm rounded-md font-semibold text-primary text-label-md">Minggu</button>
                    <button
                        class="px-6 py-2 rounded-md font-medium text-muted hover:text-primary text-label-md transition-colors">Bulan</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <!-- Day Column: Monday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Sen</span>
                        <span class="font-display text-headline-md text-primary">12</span>
                    </div>
                    <div class="p-4 bg-primary text-white rounded-lg shadow-md border-l-4 border-secondary-container">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold opacity-80 uppercase">Impor</span>
                            <span class="material-symbols-outlined text-[18px]"
                                data-icon="directions_boat">directions_boat</span>
                        </div>
                        <p class="font-bold text-body-md leading-tight mb-1">MSC AVALON</p>
                        <p class="text-[11px] opacity-70 mb-3">Tj. Priok - JKT</p>
                        <div class="flex items-center gap-1 text-[10px] bg-white/10 py-1 px-2 rounded-full w-fit">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            08:30 WIB
                        </div>
                    </div>
                </div>
                <!-- Day Column: Tuesday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Sel</span>
                        <span class="font-display text-headline-md text-primary">13</span>
                    </div>
                    <div
                        class="p-4 bg-secondary-fixed text-on-secondary-container rounded-lg border border-secondary-container/20">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold text-primary opacity-80 uppercase">Lokal</span>
                            <span class="material-symbols-outlined text-[18px] text-primary"
                                data-icon="local_shipping">local_shipping</span>
                        </div>
                        <p class="font-bold text-body-md text-primary leading-tight mb-1">Grup Armada A</p>
                        <p class="text-[11px] text-primary opacity-70 mb-3">Surabaya - Gudang</p>
                        <div
                            class="flex items-center gap-1 text-[10px] bg-primary/5 py-1 px-2 rounded-full w-fit text-primary">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            14:00 WIB
                        </div>
                    </div>
                </div>
                <!-- Day Column: Wednesday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Rab</span>
                        <span class="font-display text-headline-md text-primary">14</span>
                    </div>
                    <div class="p-4 bg-primary text-white rounded-lg shadow-md border-l-4 border-secondary-container">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold opacity-80 uppercase">Impor</span>
                            <span class="material-symbols-outlined text-[18px]"
                                data-icon="flight_land">flight_land</span>
                        </div>
                        <p class="font-bold text-body-md leading-tight mb-1">FEDEX 4821</p>
                        <p class="text-[11px] opacity-70 mb-3">Soekarno Hatta</p>
                        <div class="flex items-center gap-1 text-[10px] bg-white/10 py-1 px-2 rounded-full w-fit">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            10:45 WIB
                        </div>
                    </div>
                    <div
                        class="p-4 bg-secondary-fixed text-on-secondary-container rounded-lg border border-secondary-container/20">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold text-primary opacity-80 uppercase">Lokal</span>
                            <span class="material-symbols-outlined text-[18px] text-primary"
                                data-icon="local_shipping">local_shipping</span>
                        </div>
                        <p class="font-bold text-body-md text-primary leading-tight mb-1">LogiLink-V2</p>
                        <p class="text-[11px] text-primary opacity-70 mb-3">Pelabuhan Semarang</p>
                        <div
                            class="flex items-center gap-1 text-[10px] bg-primary/5 py-1 px-2 rounded-full w-fit text-primary">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            16:15 WIB
                        </div>
                    </div>
                </div>
                <!-- Day Column: Thursday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Kam</span>
                        <span class="font-display text-headline-md text-primary">15</span>
                    </div>
                    <div
                        class="h-24 bg-surface-container-low rounded-lg border border-dashed border-outline-variant flex items-center justify-center">
                        <span class="text-label-sm text-outline">Tidak Ada Kedatangan</span>
                    </div>
                </div>
                <!-- Day Column: Friday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Jum</span>
                        <span class="font-display text-headline-md text-primary">16</span>
                    </div>
                    <div class="p-4 bg-primary text-white rounded-lg shadow-md border-l-4 border-secondary-container">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold opacity-80 uppercase">Impor</span>
                            <span class="material-symbols-outlined text-[18px]"
                                data-icon="directions_boat">directions_boat</span>
                        </div>
                        <p class="font-bold text-body-md leading-tight mb-1">MAERSK LINE</p>
                        <p class="text-[11px] opacity-70 mb-3">Tj. Perak - SBY</p>
                        <div class="flex items-center gap-1 text-[10px] bg-white/10 py-1 px-2 rounded-full w-fit">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            05:00 WIB
                        </div>
                    </div>
                </div>
                <!-- Day Column: Saturday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Sab</span>
                        <span class="font-display text-headline-md text-primary">17</span>
                    </div>
                    <div
                        class="h-24 bg-surface-container-low rounded-lg border border-dashed border-outline-variant flex items-center justify-center">
                        <span class="text-label-sm text-outline">Tidak Ada Kedatangan</span>
                    </div>
                </div>
                <!-- Day Column: Sunday -->
                <div class="flex flex-col gap-4">
                    <div class="text-center py-2 border-b border-border mb-2">
                        <span class="block text-label-sm uppercase tracking-wider text-muted">Min</span>
                        <span class="font-display text-headline-md text-primary">18</span>
                    </div>
                    <div
                        class="p-4 bg-secondary-fixed text-on-secondary-container rounded-lg border border-secondary-container/20">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-label-sm font-bold text-primary opacity-80 uppercase">Lokal</span>
                            <span class="material-symbols-outlined text-[18px] text-primary"
                                data-icon="local_shipping">local_shipping</span>
                        </div>
                        <p class="font-bold text-body-md text-primary leading-tight mb-1">Ops Akhir Pekan</p>
                        <p class="text-[11px] text-primary opacity-70 mb-3">Batam Free Zone</p>
                        <div
                            class="flex items-center gap-1 text-[10px] bg-primary/5 py-1 px-2 rounded-full w-fit text-primary">
                            <span class="material-symbols-outlined text-[12px]" data-icon="schedule">schedule</span>
                            23:30 WIB
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Ongoing Shipment Timeline Section -->
        <section class="py-xl bg-surface-container-low">
            <div class="px-margin-desktop max-w-container-max mx-auto">
                <div class="mb-8">
                    <h2 class="font-display text-headline-lg text-primary mb-2">Timeline Pengiriman Berjalan</h2>
                    <p class="text-body-md text-muted">Status real-time dari pengiriman kritis di seluruh jaringan
                        rantai pasok.</p>
                </div>
                <div class="space-y-6">
                    <!-- Control Tower Shipment Card 1 -->
                    <div class="bg-surface rounded-xl border border-border shadow-sm overflow-hidden transition-all">
                        <div class="p-6 border-b border-border bg-surface-container-lowest/50">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-primary-fixed flex items-center justify-center rounded-lg border border-primary/10">
                                        <span class="material-symbols-outlined text-primary font-bold">package_2</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-title-lg text-title-lg text-primary">PO #2024-88912</h4>
                                            <span
                                                class="px-2 py-0.5 bg-success/10 text-success text-[10px] font-bold rounded border border-success/20 uppercase tracking-tighter">Green
                                                Lane</span>
                                        </div>
                                        <p class="text-muted text-body-md">Shenzhen Tech Pro, LTD <span
                                                class="mx-2">•</span> Tiongkok <span
                                                class="material-symbols-outlined text-xs align-middle">flag</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div class="text-center">
                                        <p class="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">ETD
                                            (KEBERANGKATAN)</p>
                                        <p class="font-label-md text-on-surface">12 Mar 2024</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">ETA
                                            (KEDATANGAN)</p>
                                        <p class="font-label-md text-on-surface">05 Apr 2024</p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-[10px] text-muted uppercase font-bold">PROGRES</p>
                                            <p class="text-label-md font-bold text-primary">68%</p>
                                        </div>
                                        <div class="w-32 h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                                            <div class="w-[68%] h-full bg-primary rounded-full"></div>
                                        </div>
                                    </div>
                                    <button class="p-2 hover:bg-surface-container rounded-full transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 relative overflow-x-auto custom-scrollbar no-scrollbar">
                            <div class="min-w-[1200px] flex justify-between">
                                <!-- Step 1 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">PO Dibuat</p>
                                </div>
                                <!-- Step 2 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Dikonfirmasi</p>
                                </div>
                                <!-- Step 3 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Produksi</p>
                                </div>
                                <!-- Step 4 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Barang Siap</p>
                                </div>
                                <!-- Step 5 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Booking Kapal</p>
                                </div>
                                <!-- Step 6 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">ETD</p>
                                </div>
                                <!-- Step 7 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Berangkat</p>
                                </div>
                                <!-- Step 8 (Active) -->
                                <div class="relative flex flex-col items-center w-24 timeline-step step-active">
                                    <div
                                        class="w-6 h-6 rounded-full bg-primary flex items-center justify-center z-10 border-4 border-white shadow-lg shadow-primary/20 mb-3 animate-pulse">
                                        <span
                                            class="material-symbols-outlined text-[12px] text-white font-bold">directions_boat</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-primary uppercase text-center">Perjalanan</p>
                                </div>
                                <!-- Step 9 -->
                                <div class="relative flex flex-col items-center w-24 timeline-step">
                                    <div
                                        class="w-6 h-6 rounded-full bg-surface-container-high flex items-center justify-center z-10 border-2 border-border mb-3">
                                    </div>
                                    <p class="text-[9px] font-bold text-muted uppercase text-center">Tiba di Gudang</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="px-6 py-3 bg-surface-container-low/30 border-t border-border flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-bold text-muted uppercase tracking-widest">UPDATE
                                    TERAKHIR:</span>
                                <span class="text-[11px] font-medium text-on-surface-variant flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">schedule</span>
                                    25 Mar 2024, 09:42 WIB - Posisi: Selat Malaka
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    class="px-3 py-1.5 text-primary text-label-md font-semibold hover:bg-primary/5 rounded-md transition-all">Unduh
                                    B/L</button>
                                <button
                                    class="px-3 py-1.5 bg-primary text-white text-label-md font-semibold rounded-md shadow-md shadow-primary/10">Lihat
                                    Detail</button>
                            </div>
                        </div>
                    </div>
                    <!-- Control Tower Shipment Card 2 (Exception) -->
                    <div class="bg-surface rounded-xl border border-border shadow-sm overflow-hidden transition-all">
                        <div class="p-6 border-b border-border bg-surface-container-lowest/50">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-danger/10 flex items-center justify-center rounded-lg border border-danger/20">
                                        <span class="material-symbols-outlined text-danger font-bold">warning</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-title-lg text-title-lg text-primary">PO #2024-88751</h4>
                                            <span
                                                class="px-2 py-0.5 bg-danger/10 text-danger text-[10px] font-bold rounded border border-danger/20 uppercase tracking-tighter">Red
                                                Lane</span>
                                        </div>
                                        <p class="text-muted text-body-md">Global Electronics GMBH <span
                                                class="mx-2">•</span> Jerman <span
                                                class="material-symbols-outlined text-xs align-middle">flag</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div class="text-center">
                                        <p class="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">ETD
                                        </p>
                                        <p class="font-label-md text-on-surface">15 Feb 2024</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">ETA
                                        </p>
                                        <p class="font-label-md text-on-surface">20 Mar 2024</p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-[10px] text-muted uppercase font-bold">PROGRES</p>
                                            <p class="text-label-md font-bold text-danger">85%</p>
                                        </div>
                                        <div class="w-32 h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                                            <div class="w-[85%] h-full bg-danger rounded-full"></div>
                                        </div>
                                    </div>
                                    <button class="p-2 hover:bg-surface-container rounded-full transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 relative overflow-x-auto custom-scrollbar no-scrollbar">
                            <div class="min-w-[1200px] flex justify-between">
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">PO Dibuat</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Dikonfirmasi</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Produksi</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Barang Siap</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">Booking</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-done">
                                    <div
                                        class="w-6 h-6 rounded-full bg-success flex items-center justify-center z-10 border-4 border-white shadow-sm mb-3">
                                        <span
                                            class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-success uppercase text-center">ETD</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step step-active">
                                    <div
                                        class="w-8 h-8 rounded-full bg-danger flex items-center justify-center z-10 border-4 border-white shadow-lg shadow-danger/20 mb-2">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-white font-bold">priority_high</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-danger uppercase text-center">Kapal Berangkat
                                    </p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step">
                                    <div
                                        class="w-6 h-6 rounded-full bg-surface-container-high border-2 border-border mb-3">
                                    </div>
                                    <p class="text-[9px] font-bold text-muted uppercase text-center">Perjalanan</p>
                                </div>
                                <div class="relative flex flex-col items-center w-24 timeline-step">
                                    <div
                                        class="w-6 h-6 rounded-full bg-surface-container-high border-2 border-border mb-3">
                                    </div>
                                    <p class="text-[9px] font-bold text-muted uppercase text-center">Tiba di Gudang</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-danger/5 border-t border-danger/10 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-bold text-danger uppercase tracking-widest">UPDATE
                                    TERAKHIR:</span>
                                <span class="text-[11px] font-medium text-danger flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">warning</span>
                                    Berkas Pabean membutuhkan atensi - Ketidaksesuaian nilai HS Code
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    class="px-4 py-2 bg-danger text-white text-label-md font-semibold rounded-md flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">edit_document</span> Revisi Dokumen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Knowledge Base -->
        <section class="py-xl px-margin-desktop max-w-container-max mx-auto">
            <div class="mb-12">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="font-display text-headline-lg text-primary mb-2">Pusat Pengetahuan PT. CSA</h2>
                        <p class="text-body-md text-muted">Pembaruan kritis dan sumber daya kepatuhan bagi profesional
                            logistik.</p>
                    </div>
                    <a class="flex items-center gap-2 text-primary font-bold text-label-md hover:gap-3 transition-all mb-1"
                        href="#">
                        Lihat Semua <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- KB Card 1 -->
                <div class="group cursor-pointer">
                    <div class="relative h-64 rounded-xl overflow-hidden mb-4 shadow-lg">
                        <img alt="Regulasi Impor"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFjlaweV_5k3HuVcnLxJ5JyQGF872m-BS-ILVQY2sNtEyYxGFSBgnncSpVlJN8pqBt5BYaLYkglTI5E1lG31fQtz3AiimfhpglHYVPOnogVr3mjh0Z2ImLDpRIbVZuPkMEEtk1R4GOnSVoKDl8SnmZx4VUL2jkxx04qQL86SxQYCvNzM2l9cLSG1nNKJjMqQxXAecmmmu3AUDbUAeNFddBQkmHFdJShzvvMW4uTfQk2BStBG0OY4DoYz7SqNoEn0iikUtId7va5e4b">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <span
                                class="bg-white/20 backdrop-blur-md text-white text-label-sm px-3 py-1 rounded-full border border-white/30">Kepatuhan</span>
                        </div>
                    </div>
                    <h3
                        class="font-display text-headline-md text-primary mb-3 group-hover:text-secondary transition-colors">
                        Regulasi Impor</h3>
                    <p class="text-body-md text-muted mb-4">Pembaruan terbaru mengenai perubahan regulasi impor
                        Indonesia untuk periode fiskal Q4 2025.</p>
                    <a class="flex items-center gap-2 text-primary font-bold text-label-md hover:gap-3 transition-all"
                        href="#">Login untuk Analisis Lengkap <span class="material-symbols-outlined">lock</span></a>
                </div>
                <!-- KB Card 2 -->
                <div class="group cursor-pointer">
                    <div class="relative h-64 rounded-xl overflow-hidden mb-4 shadow-lg">
                        <img alt="Prosedur Bea Cukai"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2rZHvTfvKMWX5vaUKhtJTOnIwxBGpKtpdA9JbCb3ya29jaR-J8QZGNCuC_g2qJc2jQ5IwiACYTRRDuzkjlHQZijE29Q7oj8GIvg-CisedCA7B5nau4_WrTVuy3yTrlVPYzjqXC-2vW3d34d5Ra6wVFc4uv_oUECYjZbOU9dCJx-YSICywA23MelhnjADpr2a-T1h7EtD26SATvMUnO__a_JZYU3UQiaNl3DeN8McmJeYt756nTZ4QQTtf6RflvdRaDAs3LdNRAn3q">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <span
                                class="bg-white/20 backdrop-blur-md text-white text-label-sm px-3 py-1 rounded-full border border-white/30">Prosedur</span>
                        </div>
                    </div>
                    <h3
                        class="font-display text-headline-md text-primary mb-3 group-hover:text-secondary transition-colors">
                        Prosedur Bea Cukai</h3>
                    <p class="text-body-md text-muted mb-4">Panduan langkah demi langkah untuk mempercepat penyelesaian
                        bea cukai di pelabuhan utama Indonesia.</p>
                    <a class="flex items-center gap-2 text-primary font-bold text-label-md hover:gap-3 transition-all"
                        href="#">Login untuk Analisis Lengkap <span class="material-symbols-outlined">lock</span></a>
                </div>
                <!-- KB Card 3 -->
                <div class="group cursor-pointer">
                    <div class="relative h-64 rounded-xl overflow-hidden mb-4 shadow-lg">
                        <img alt="Cek LARTAS"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfojcZq811agXfcFyUlg-s5VKOMd-GHoiUzXJGnl5M7eE9hAF7nTqywdMAhBHCVp5jCF7ccj8rNg0zWf0UOX2D_TWsNNw6FFYr0zJX3XwjH2Wc6q2e3bLzzEAp78G0SPx-1e9ymH_C5_QTpC9aiJUWYtUQlJu6SKcH2AENsjGEWLi5bnGKdESJrfR8gL90bd6D_N8-nmwqtOHR0qLS1kw_Rgypty8r0sWFjGAT-Ltd4jJ9hj3tBEUssCgz2bRe3JKb9G1hNOlmjsBP">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <span
                                class="bg-white/20 backdrop-blur-md text-white text-label-sm px-3 py-1 rounded-full border border-white/30">Cek
                                LARTAS</span>
                        </div>
                    </div>
                    <h3
                        class="font-display text-headline-md text-primary mb-3 group-hover:text-secondary transition-colors">
                        Cek LARTAS</h3>
                    <p class="text-body-md text-muted mb-4">Cari di database terintegrasi untuk daftar barang yang
                        dilarang atau dibatasi (LARTAS) tahun 2026.</p>
                    <a class="flex items-center gap-2 text-primary font-bold text-label-md hover:gap-3 transition-all"
                        href="#">Login untuk Analisis Lengkap <span class="material-symbols-outlined">lock</span></a>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
