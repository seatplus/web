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
            const urlParams = new URLSearchParams(window.location.search)
            const ids = urlParams.getAll('character_ids[]')

            if (!ids.length) return null

            return ids.map(id => parseInt(id))
        }
    }
}
</script>

<style scoped>

</style>