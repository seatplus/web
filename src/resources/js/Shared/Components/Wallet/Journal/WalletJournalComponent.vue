<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="flex-grow"
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

      <ul class="relative z-0 divide-y divide-gray-200">
        <InfiniteLoadingHelper
          :route="route"
          :params="routeParameters"
          @result="(result) => assets_data = result"
        >
          <WalletJournalRowComponent
            v-for="(entry, index) in assets_data"
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
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import WalletJournalRowComponent from "./WalletJournalRowComponent";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";

export default {
    name: "WalletJournalComponent",
    components: {
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
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            assets_data: []
        }
    },
    computed: {
        route() {
            return this.division? 'corporation.wallet_journal.detail' : 'character.wallet_journal.detail'
        },
        routeParameters() {
            return this.division ? {
                corporation_id: this.id,
                division_id: this.division.division_id
            } : {
                character_id: this.id
            }
        }
    },
    created() {
        this.infiniteId += 1;
    }
}
</script>

<style scoped>

</style>
