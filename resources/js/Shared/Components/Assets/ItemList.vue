<template>
  <div class="divide-y divide-gray-200">
    <CompactAssetListComponent
      v-if="compact"
      :items="items"
    />
    <WideAssetListComponent
      v-else
      :items="items"
    />
  </div>
</template>

<script setup>

  import CompactAssetListComponent from "./CompactAssetListComponent.vue";
  import WideAssetListComponent from "./WideAssetListComponent.vue";

  defineProps({
    items: {
      required: true,
      type: Array
    },
    compact: {
      required: true,
      default: false,
      type: Boolean
    }
  })
</script>

<style scoped>

</style>
