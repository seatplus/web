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

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";

const page = usePage();

const selectedIds = computed(() => {
    // Read the character_ids query params off the current URL (replacing Ziggy's
    // route().params); supports both bracketed and plain array notation.
    const query = page.url.split('?')[1] ?? ''
    const params = new URLSearchParams(query)
    const selectedCharacterIds = params.getAll('character_ids[]').concat(params.getAll('character_ids'))

    return _.map(selectedCharacterIds, (id) => parseInt(id))
})
</script>

<style scoped>

</style>