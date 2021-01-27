<template>
    <CardWithHeader>
        <template v-slot:header>
            <div class="flex">
                <EntityByIdBlock class="flex-grow" :id="id" />
                <div class="flex-none text-right text-sm text-gray-500">Transaction</div>
            </div>
        </template>

        <div>
            <div class="flex flex-col max-h-96">
                <div class="flex-grow overflow-y-auto overflow-x-auto">
                    <table class="relative table-fixed w-full">
                        <thead class="sticky top-0 bg-gray-50">
                        <TableHeader>
                            <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </DataHeader>
                            <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span class="sr-only">Brought or Sold</span>
                            </DataHeader>
                            <DataHeader class="w-6/12 sticky top-0 px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </DataHeader>
                            <DataHeader class="w-1/6 sticky top-0 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </DataHeader>
                            <DataHeader class="w-1/6 relative px-6 py-3">
                                <span class="sr-only">Expand</span>
                            </DataHeader>
                        </TableHeader>
                        </thead>
                        <tbody>
                        <WalletTransactionRowComponent v-for="(entry, index) in transactions" :entry="entry" :key="entry.transaction_id" :even="index%2" />
                        <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                            <div slot="no-more">all loaded</div>
                        </infinite-loading>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
import InfiniteLoading from "vue-infinite-loading";
import WalletTransactionRowComponent from "./WalletTransactionRowComponent";

export default {
    name: "WalletTransactionComponent",
    components: {
        WalletTransactionRowComponent,
        DataHeader, TableHeader, EntityByIdBlock, CardWithHeader, InfiniteLoading},
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
            transactions: [],
            page: 1,
        }
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.$route('character.wallet_transaction.detail', this.id), {
                params: {
                    page: this.page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.page += 1;
                    self.transactions.push(...response.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    },
    created() {
        this.infiniteId += 1;
    }
}
</script>

<style scoped>

</style>
