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
          route="character.wallet_journal.detail"
          :params="id"
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
<!--    <div class="">
      <div class="flex flex-col max-h-96">
        <div class="flex-grow overflow-y-auto overflow-x-hidden">
          <table class="relative table-fixed w-full">
            <thead class="sticky top-0 bg-gray-50">
              <TableHeader>
                <DataHeader class="w-2/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase overflow-hidden tracking-wider">
                  Date
                </DataHeader>
                <DataHeader class="w-2/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </DataHeader>
                <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Amount
                </DataHeader>
                <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Balance
                </DataHeader>
                <DataHeader class="w-2/12 relative px-6 py-3">
                  <span class="sr-only">Expand</span>
                </DataHeader>
              </TableHeader>
            </thead>
            <tbody>
              <InfiniteLoadingHelper
                route="character.wallet_journal.detail"
                :params="id"
                @result="(result) => assets_data = result"
              >
                <WalletJournalRowComponent
                  v-for="(entry, index) in assets_data"
                  :key="entry.id"
                  :entry="entry"
                  :even="index%2"
                />
              </InfiniteLoadingHelper>

              &lt;!&ndash;                        <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                            <div slot="no-more">all loaded</div>
                        </infinite-loading>&ndash;&gt;
            </tbody>
          </table>
        </div>
      </div>
    </div>-->
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
// TODO: Infinite Loading
//import InfiniteLoading from "vue-infinite-loading";
import WalletJournalRowComponent from "./WalletJournalRowComponent";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import InfiniteLoadingHelper from "../../../InfiniteLoadingHelper";

export default {
    name: "WalletJournalComponent",
    components: {
        InfiniteLoadingHelper,
        EntityByIdBlock,
        WalletJournalRowComponent,
        DataHeader, TableHeader,
        //InfiniteLoading,
        CardWithHeader},
    props: {
        id: {
            required: true,
            type: Number
        },
        division: {
            required: false,
            type: Number
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            assets_data: [],
            page: 1,
        }
    },
    created() {
        this.infiniteId += 1;
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.$route('character.wallet_journal.detail', this.id), {
                params: {
                    page: this.page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.page += 1;
                    self.assets_data.push(...response.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    }
}
</script>

<style scoped>

</style>
