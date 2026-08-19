<script setup lang="ts">
import { createApp, reactive, ref } from 'vue'
import { Ref } from 'vue';
import AlertError from './AlertError.vue';
import { router } from '@inertiajs/vue3';

const selected = ref('')
const reference = ref('')
const errors = ref<string[]>([])

const options = [
    {
        label: 'Nomor BL',
        value : 'bl_number',
        page:'BLNumber'
    },
    {
        label: 'Nomor Shipment', 
        value : 'shipment_number',
        page:'ShipmentNumber'
    },
    {
        label: 'Nomor PO', 
        value : 'po_number',
        page:'PONumber'
    },
    {
        label: 'Nomor Invoice', 
        value : 'invoice_number',
        page:'InvoiceNumber'
    },
    {
        label: 'Barcode', 
        value : 'product_code',
        page:'ProductCode'
    }
]

const search = () => {
    errors.value = []

    // Validasi pilihan
    if (!selected.value) {
        errors.value.push('Silakan pilih jenis pencarian terlebih dahulu.')
        return
    }

    // Validasi input
    if (!reference.value.trim()) {
        errors.value.push('Silakan masukkan nomor referensi.')
        return
    }

    // Cari option yang dipilih
    const option = options.find(
        (item) => item.value === selected.value
    )

    if (!option) {
        errors.value.push('Jenis pencarian tidak valid.')
        return
    }

    // Redirect ke page sesuai pilihan
    router.get(`/search/${option.page}`, {
        reference: reference.value.trim(),
    })
}
</script>

<template>
    <div class="flex w-full items-center space-x-2 relative flex-shrink-0">
       
        <select v-model="selected"
            class="md:w-48 bg-surface-container-low border-none rounded-lg font-medium text-on-surface-variant focus:ring-2 focus:ring-primary h-14 pl-4 pr-10">
            <option disabled value="">Pilih</option>
            <option 
                class="text-on-primary" 
                v-for="option in options"
                :key="option.value"
                :value="option.value">
                {{ option.label }}
            </option>
            </select>                  
        <!--Reference Input-->
        <div class="flex-grow relative">
            <input 
                class="w-full h-14 bg-white border-black focus:ring-2 focus:ring-primary rounded-lg text-body-lg"
                placeholder=" Masukkan nomor referensi (misal: CSA-99210-BL)" 
                type="text" 
                id="reference"
                v-model="reference"
                @keyup.enter="search"
                />
        </div>
        <!--Search Buton-->
        <button 
            type="button"
            @click="search"
            class="bg-primary text-white px-8 h-14 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-opacity-90 transition-all shrink-0">
        <span class="material-symbols-outlined" data-icon="search">Search</span>
    </button>
    </div>
    
</template>
