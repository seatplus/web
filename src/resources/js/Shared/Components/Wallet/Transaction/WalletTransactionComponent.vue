<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="flex-grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Transaction
        </div>
      </div>
    </template>

    <div class="relative max-h-96 overflow-y-auto">
      <div class="hidden sm:grid sm:grid-cols-12 sm:gap-x-0 sm:gap-y-0.5 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-6 sm:px-3 py-1">
          Date
        </div>
        <div class="px-6 sm:px-3 py-1">
          <span class="sr-only">Brought or Sold</span>
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-6">
          Type
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Total
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          <span class="sr-only">Expand</span>
        </div>
      </div>

      <ul class="relative z-0 divide-y divide-gray-200">
        <InfiniteLoadingHelper
          :route="route"
          :params="routeParameters"
          @result="(result) => transactions = result"
        >
          <WalletTransactionRowComponent
            v-for="(entry, index) in transactions"
            :key="entry.transaction_id"
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
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import WalletTransactionRowComponent from "./WalletTransactionRowComponent";
import InfiniteLoadingHelper from "../../../InfiniteLoadingHelper";

export default {
    name: "WalletTransactionComponent",
    components: {
        InfiniteLoadingHelper,
        WalletTransactionRowComponent,
         EntityByIdBlock, CardWithHeader
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
            transactions: []
        }
    },
    created() {
        this.infiniteId += 1;
    },
    computed: {
        route() {
            return this.division? 'corporation.wallet_transaction.detail' : 'character.wallet_transaction.detail'
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
}
</script>

<style scoped>

</style>
