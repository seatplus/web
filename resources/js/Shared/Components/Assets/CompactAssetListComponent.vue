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

<script>
import CompactAssetListElement from "./CompactAssetListElement.vue";

export default {
    name: "CompactAssetListComponent",
    components: {CompactAssetListElement,},
    props: {
        items: {
            required: true,
            type: Array
        }
    },
    computed: {
        uniqueItems() {
            return _.uniqBy(this.items, 'item_id')
        },
        hasOwnerPicture() {

            let selectedCharacterIds = _.get(route().params, 'character_ids', null)

            if (_.size(selectedCharacterIds) > 1)
                return true

            return !selectedCharacterIds && this.$page.props.user.data.characters.length > 1;
        }
    }
}
</script>

<style scoped>

</style>