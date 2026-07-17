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
         <InfiniteScroll> merges the next page without jumping to top. -->
    <div
      class="relative max-h-96 overflow-y-auto"
      scroll-region=""
    >
      <!-- Both the character page and the recruitment/watchlist tab deliver contracts as a
           page scroll prop (keyed via scrollKey) → native Inertia <InfiniteScroll>. -->
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
        // Page scroll prop key holding this list's contracts paginator. The character page
        // passes `contracts_<id>`; the recruitment tab passes `contracts_<id>` (all) or
        // `watchlisted_contracts_<id>` (watchlist-filtered) depending on the active sub-tab.
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
