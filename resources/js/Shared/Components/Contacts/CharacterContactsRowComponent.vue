<template>
  <StickyHeaderTableRow :number-columns="numberColumns">
    <StickyHeaderCell
      :cell="columns[0]"
      class="self-center whitespace-normal"
    >
      <EntityByIdBlock
        :id="entry.contact_id"
        :image-size="10"
        name-font-size="text-base"
      />
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[1]"
      class="self-center whitespace-normal"
    >
      <div class="flex space-x-1 space-y-1 flex-wrap flex-row-reverse">
        <span
          v-for="label in entry.labels"
          :key="label.id"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
        >
          {{ label.label_name }}
        </span>
      </div>
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[2]"
      class="self-center whitespace-normal"
    >
      {{ entry.standing }}
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[3]"
      class="self-center whitespace-normal"
    >
      {{ entry.corporation_standing != null ? entry.corporation_standing : 'N.A.' }}
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[4]"
      class="self-center whitespace-normal"
    >
      {{ entry.alliance_standing != null ? entry.alliance_standing : 'N.A.' }}
    </StickyHeaderCell>
  </StickyHeaderTableRow>
</template>

<script setup>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow.vue";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell.vue";

defineProps({
    entry: {
        required: true,
        type: Object,
    },
    columns: {
        required: true,
        type: Array,
    },
    numberColumns: {
        required: true,
        type: Number,
    },
});
</script>

<style scoped>

</style>
