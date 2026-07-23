<template>
  <div class="hidden sm:grid sm:grid-cols-8 sm:gap-x-0 sm:gap-y-0.5 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
    <div class="px-6 sm:px-3 py-1 col-span-1">
      Quantity
    </div>
    <div class="px-6 sm:px-3 py-1 col-span-3">
      Type
    </div>
    <div class="px-6 sm:px-3 py-1 col-span-1">
      Volume
    </div>
    <div class="px-6 sm:px-3 py-1 col-span-2">
      Group
    </div>
    <div class="px-6 sm:px-3 py-1 col-span-1">
      <span class="sr-only">Inspect</span>
    </div>
  </div>
  <CompactAssetListElement
    v-for="(item, index) in items"
    :key="item.item_id"
    :entry="item"
    :even="index%2"
  />
</template>

<script setup>
import CompactAssetListElement from "./CompactAssetListElement.vue";

defineProps({
    items: {
        required: true,
        type: Array
    }
});
</script>

<style scoped>

</style>