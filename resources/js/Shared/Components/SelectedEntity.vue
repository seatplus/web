<template>
  <div
    v-if="selectedIds"
    class="grid gap-1 grid-flow-col-dense auto-cols-auto"
  >
    <label>Selected Entities</label>
    <div
      v-for="id in selectedIds"
      :key="id"
    >
      <EntityByIdBlock
        :id="id"
        :image-size="5"
        name-font-size="sm"
        :with-sub-text="false"
      />
    </div>
  </div>
</template>

<script>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
export default {
    name: "SelectedEntity",
    components: {EntityByIdBlock},
    computed: {
        selectedIds() {
            let selectedCharacterIds = _.get(this.$route().params, 'character_ids', null)

            return _.map(selectedCharacterIds, (id) => parseInt(id))
        }
    }
}
</script>

<style scoped>

</style>