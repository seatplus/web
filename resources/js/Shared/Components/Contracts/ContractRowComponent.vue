<template>
  <StickyHeaderTableRow :number-columns="countColumns">
    <!-- Issuer -->
    <StickyHeaderCell
      :cell="columns[0]"
      class="self-center truncate"
    >
      <IssuerComponent :contract="contract" />
    </StickyHeaderCell>
    <!-- Assignee -->
    <StickyHeaderCell
      :cell="columns[1]"
      class="self-center truncate"
    >
      <AssigneeComponent :contract="contract" />
    </StickyHeaderCell>
    <!-- Contract type -->
    <StickyHeaderCell
      :cell="columns[2]"
      class="flex flex-wrap text-sm font-medium text-gray-500 sm:self-center truncate"
    >
      <ContractTypeComponent :contract="contract" />
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[3]"
      class="flex flex-wrap gap-x-2 text-sm font-medium text-gray-500 sm:self-center"
    >
      <DetailsComponent :contract="contract" />
    </StickyHeaderCell>
    <StickyHeaderCell
      :cell="columns[4]"
      class="sm:self-center sm:place-self-end"
    >
      <ExpandContractComponent
        v-if="contract.items > 0"
        :character-id="characterId"
        :contract="contract"
      />
    </StickyHeaderCell>
  </StickyHeaderTableRow>
</template>

<script setup>
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow.vue";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell.vue";
import ExpandContractComponent from "./ExpandContractComponent.vue";
import IssuerComponent from "./Cells/IssuerComponent.vue";
import AssigneeComponent from "./Cells/AssigneeComponent.vue";
import ContractTypeComponent from "./Cells/ContractTypeComponent.vue";
import DetailsComponent from "./Cells/DetailsComponent.vue";

defineProps({
    contract: {
        required: true,
        type: Object,
    },
    // The StickyHeaderTable slot's column definitions + count, threaded through so
    // the cells lay out identically whether fed by InfiniteScroll or the helper.
    columns: {
        required: true,
        type: Array,
    },
    countColumns: {
        required: true,
        type: Number,
    },
    characterId: {
        required: true,
        type: Number,
    },
});
</script>
