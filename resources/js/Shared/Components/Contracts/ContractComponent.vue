<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Contract
        </div>
      </div>
    </template>

    <!-- scroll-region lets Inertia track/restore this custom scroll container so
         <InfiniteScroll> merges the next page without jumping to top. Contracts are delivered as a
         page scroll prop (the character page's contracts_<id>, or the recruitment/observation
         watchlist_contracts_<id>) — see the scrollKey the parent passes. -->
    <div
      class="relative max-h-96 overflow-y-auto"
      scroll-region=""
    >
      <InfiniteScroll
        :data="scrollKey"
        :items-element="`#${scrollBodyId}`"
        preserve-url
      >
        <StickyHeaderTable
          :header-titles="headerTitles"
          :body-id="scrollBodyId"
        >
          <template #default="slotProps">
            <ContractRowComponent
              v-for="contract in scrollEntries"
              :key="contract.contract_id"
              :contract="contract"
              :columns="slotProps.columns"
              :count-columns="slotProps.countColumns"
              :character-id="id"
            />
          </template>
        </StickyHeaderTable>
      </InfiniteScroll>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable.vue";
import { InfiniteScroll } from "@inertiajs/vue3";
import ContractRowComponent from "./ContractRowComponent.vue";

export default {
    name: "ContractComponent",
    components: {
        InfiniteScroll,
        ContractRowComponent,
        StickyHeaderTable,
        CardWithHeader,
        EntityByIdBlock,
    },
    props: {
        id: {
            required: true,
            type: Number
        },
        type: {
            required: false,
            type: String,
            default: 'character'
        },
        // The page scroll prop this card renders — contracts_<id> (all) on the character page and
        // the "All" recruitment sub-tab, or watchlist_contracts_<id> on the "Watchlisted" sub-tab.
        scrollKey: {
            required: true,
            type: String,
        },
    },
    computed: {
        headerTitles() {
            return [
                {title: 'Issuer', columnSpan: 2},
                {title: 'Assignee', columnSpan: 2},
                {title: 'Type', columnSpan: 2},
                {title: 'Details', columnSpan: 4},
                {title: 'Content', columnSpan: 1, srOnly: true},
            ]
        },
        scrollBodyId() {
            return `contracts-body-${this.id}`
        },
        scrollEntries() {
            return this.$page.props[this.scrollKey]?.data ?? []
        },
    }
}
</script>
