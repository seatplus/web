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
      :scroll-region="scrollKey ? '' : null"
    >
      <!-- Character page: contracts delivered as a page scroll prop → Inertia InfiniteScroll. -->
      <InfiniteScroll
        v-if="scrollKey"
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

      <!-- Recruitment (watchlist): still the axios helper against the details endpoint. -->
      <InfiniteLoadingHelper
        v-else
        v-slot="{results}"
        route-name="character.contracts.details"
        :params="parameters"
      >
        <StickyHeaderTable :header-titles="headerTitles">
          <template #default="slotProps">
            <ContractRowComponent
              v-for="contract in results"
              :key="contract.contract_id"
              :contract="contract"
              :columns="slotProps.columns"
              :count-columns="slotProps.countColumns"
              :character-id="id"
            />
          </template>
        </StickyHeaderTable>
      </InfiniteLoadingHelper>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable.vue";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";
import { InfiniteScroll } from "@inertiajs/vue3";
import ContractRowComponent from "./ContractRowComponent.vue";

export default {
    name: "ContractComponent",
    components: {
        InfiniteScroll,
        ContractRowComponent,
        InfiniteLoadingHelper,
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
        watchlist: {
            required: false,
            type: Object,
            default: () => new Object()
        },
        // When set (character page) contracts come from this page scroll prop via
        // <InfiniteScroll>. When null (recruitment) the axios helper + watchlist is used.
        scrollKey: {
            required: false,
            type: String,
            default: null,
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
        parameters() {
            return {...this.watchlist, character_id: this.id}
        }
    }
}
</script>
