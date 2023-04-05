<template>
  <div class="divide-y divide-gray-200">
    <CompactAssetListComponent
      v-if="compact"
      :items="uniqueItems"
    />
    <WideAssetListComponent
      v-else
      :items="uniqueItems"
    />
  </div>
</template>

<script>

  import CompactAssetListComponent from "./CompactAssetListComponent.vue";
  import WideAssetListComponent from "./WideAssetListComponent.vue";

  export default {
    name: "ItemList",
        components: {CompactAssetListComponent, WideAssetListComponent},
        props: {
            compact: {
                required: true,
                default: false,
                type: Boolean
            },
            items: {
                required: true,
                type: Array
            }
        },
        computed: {
            uniqueItems() {
                return _.uniqBy(this.items, 'item_id')
            },
        },
  }
</script>

<style scoped>

</style>
