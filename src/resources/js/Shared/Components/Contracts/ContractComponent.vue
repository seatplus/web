<template>
    <CardWithHeader>
        <template v-slot:header>
            <div class="flex">
                <EntityByIdBlock class="flex-grow" :id="id" />
                <div class="flex-none text-right text-sm text-gray-500">Contract</div>
            </div>
        </template>
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="relative max-h-96 overflow-y-auto">
            <div class="hidden sm:block z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500 sm:grid sm:grid-cols-5 sm:gap-1 grid-flow-row ">
                <div class="px-6 py-1">Issuer</div>
                <div class="px-6 py-1">Assignee</div>
                <div class="px-6 py-1">Type</div>
                <div class="px-6 py-1">Title</div>
                <div class="px-6 py-1">Details</div>
            </div>

            <ul class="relative z-0 divide-y divide-gray-200">
                <ContractRowComponent :contract="contract" v-for="contract in data" :key="contract.contract_id" :entity="entity" />

                <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                    <div slot="no-more">all loaded</div>
                </infinite-loading>
            </ul>

        </div>


    </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import ContractRowComponent from "./ContractRowComponent";
import InfiniteLoading from "vue-infinite-loading";
export default {
    name: "ContractComponent",
    components: {ContractRowComponent, CardWithHeader, EntityByIdBlock, InfiniteLoading},
    props: {
        id: {
            required: true,
            type: Number
        },
        type: {
            required: false,
            type: String,
            default: 'character'
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            data: [],
            page: 1,
        }
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.$route('character.contracts.details', this.id), {
                params: {
                    page: this.page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.page += 1;
                    self.data.push(...response.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    },
    computed: {
        entity() {
            return {
                type: this.type,
                character_id: this.type === 'character' ? this.id : null,
                corporation_id: this.type === 'corporation' ? this.id : null
            }
        }
    }
}
</script>

<style scoped>

</style>
