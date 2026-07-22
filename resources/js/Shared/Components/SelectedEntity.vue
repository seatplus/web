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
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
export default {
    name: "SelectedEntity",
    components: {EntityByIdBlock},
    computed: {
        selectedIds() {
            // Read the character_ids query params off the current URL (replacing Ziggy's
            // route().params); supports both bracketed and plain array notation.
            const query = this.$page.url.split('?')[1] ?? ''
            const params = new URLSearchParams(query)
            const selectedCharacterIds = params.getAll('character_ids[]').concat(params.getAll('character_ids'))

            return _.map(selectedCharacterIds, (id) => parseInt(id))
        }
    }
}
</script>

<style scoped>

</style>