<template>
    <CardWithHeader>
        <template v-slot:header>
            <div class="flex">
                <EntityByIdBlock class="flex-grow" :id="id" />
                <div class="flex-none text-right text-sm text-gray-500">Journal</div>
            </div>

        </template>
        <div class="">
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
                        <WalletJournalRowComponent v-for="(entry, index) in assets_data" :entry="entry" :key="entry.id" :even="index%2" />
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
import SimpleStriped from "@/Shared/Layout/Cards/Table/SimpleStriped";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataCell from "@/Shared/Layout/Cards/Table/DataCell";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
import TableRow from "@/Shared/Layout/Cards/Table/TableRow";
import InfiniteLoading from "vue-infinite-loading";
import Time from "@/Shared/Time";
import WalletJournalRowComponent from "./WalletJournalRowComponent";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";

export default {
    name: "WalletJournalComponent",
    components: {
        EntityByIdBlock,
        WalletJournalRowComponent,
        Time, TableRow, DataHeader, DataCell, TableHeader, SimpleStriped, InfiniteLoading, CardWithHeader},
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
    },
    created() {
        this.infiniteId += 1;
    }
}
</script>

<style scoped>

</style>
