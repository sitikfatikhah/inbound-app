<script lang="ts">
</script>
<template>
    <div class="flex flex-col gap-4">
        <ShipmentDocuments
            v-if="shipmentDocuments.length > 0"
            :items="shipmentDocuments"
            description="   The following items have been allocated to this shipment."
        />
        <ShipmentDocuments
            v-else
            :items="[]"
            description="No items have been allocated to this shipment."
        />
    </div>
</template>