<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Journal
        </div>
      </div>
    </template>
    <div class="relative max-h-96 overflow-y-auto">
      <div class="hidden sm:grid sm:grid-cols-12 sm:gap-x-0 sm:gap-y-0.5 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Date
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Type
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-3">
          Amount
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-3">
          Balance
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          <span class="sr-only">Expand</span>
        </div>
      </div>

      <!-- Character wallets: native Inertia infinite scroll over a page scroll prop -->
      <InfiniteScroll
        v-if="!division"
        :data="scrollKey"
        :items-element="`#${scrollBodyId}`"
      >
        <ul
          :id="scrollBodyId"
          class="relative z-0 divide-y divide-gray-200"
        >
          <WalletJournalRowComponent
            v-for="(entry, index) in journalEntries"
            :key="entry.id"
            :entry="entry"
            :even="index%2"
          />
        </ul>
      </InfiniteScroll>

      <!-- Corporation wallets: legacy axios loader (not yet migrated to InfiniteScroll) -->
      <ul
        v-else
        class="relative z-0 divide-y divide-gray-200"
      >
        <InfiniteLoadingHelper
          v-slot="{results}"
          :route-name="routeName"
          :params="routeParameters"
        >
          <WalletJournalRowComponent
            v-for="(entry, index) in results"
            :key="entry.id"
            :entry="entry"
            :even="index%2"
          />
        </InfiniteLoadingHelper>
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import WalletJournalRowComponent from "./WalletJournalRowComponent.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import InfiniteLoadingHelper from "../../../InfiniteLoadingHelper.vue";
import { InfiniteScroll } from "@inertiajs/vue3";

export default {
    name: "WalletJournalComponent",
    components: {
        InfiniteScroll,
        InfiniteLoadingHelper,
        EntityByIdBlock,
        WalletJournalRowComponent,
        CardWithHeader
    },
    props: {
        id: {
            required: true,
            type: Number
        },
        division: {
            required: false,
            type: Object,
            default: () => {}
        },
        filters: {
            required: false,
            type: Object,
            default: () => new Object()
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
        }
    },
    computed: {
        // Character journal is delivered as a page scroll prop keyed by character id
        // (WalletsController::index), consumed by <InfiniteScroll :data="scrollKey">.
        scrollKey() {
            return `journal_${this.id}`
        },
        scrollBodyId() {
            return `journal-body-${this.id}`
        },
        journalEntries() {
            return this.$page.props[this.scrollKey]?.data ?? []
        },
        routeName() {
            return this.division? 'corporation.wallet_journal.detail' : 'character.wallet_journal.detail'
        },
        routeParameters() {

            let parameters = _.merge({ character_id: this.id }, this.filters)

            if(this.division)
                parameters = _.merge(parameters, {
                    division_id: this.division.division_id,
                    corporation_id: this.division.corporation_id
                })

            return parameters
        }
    },
    created() {
        this.infiniteId += 1;
    }
}
</script>

<style scoped>

</style>
