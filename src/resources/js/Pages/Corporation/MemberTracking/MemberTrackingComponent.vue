<template>
    <CardWithHeader>
        <template v-slot:header>
            <div class="flex">
                <EntityBlock class="flex-grow" :entity="corporation" />
            </div>
        </template>
        <!--List for small devices-->
        <div class="bg-white shadow overflow-y-auto sm:hidden sm:rounded-md">
            <ul class="divide-y divide-cool-gray-200 max-h-96">
                <MemberTrackingListElement
                    v-for="member in members" :key="member.character_id"
                    :member="member" :required_scopes="required_scopes"
                />
                <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                    <div slot="no-more">all loaded</div>
                </infinite-loading>
            </ul>

        </div>
        <!--Table for medium and above-->
        <div class="sm:flex flex-col hidden max-h-96">
            <div class="flex-grow overflow-y-auto overflow-x-hidden">
                <table class="relative table-fixed w-full">
                    <thead class="sticky top-0 bg-gray-50">
                    <TableHeader>
                        <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase overflow-hidden tracking-wider">
                            Token
                        </DataHeader>
                        <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </DataHeader>
                        <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Location
                        </DataHeader>
                        <DataHeader class="w-3/12 sticky hidden md:block top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ship
                        </DataHeader>
                        <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Joined
                        </DataHeader>
                        <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Login
                        </DataHeader>
                    </TableHeader>
                    </thead>
                    <tbody>
                    <MemberTrackingRow v-for="(member, index) in members" :member="member" :required_scopes="required_scopes" :key="member.character_id" :class="index%2 ? 'bg-gray-50' : 'bg-white'" />
<!--                    <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                        <div slot="no-more">all loaded</div>
                    </infinite-loading>-->
                    </tbody>
                </table>
            </div>
        </div>
    </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
//TODO: Infinite Loading
//import InfiniteLoading from "vue-infinite-loading";
import MemberTrackingRow from "./MemberTrackingRow";
import MemberTrackingListElement from "./MemberTrackingListElement";

export default {
    name: "MemberTrackingComponent",
    components: {
        MemberTrackingListElement,
        MemberTrackingRow, DataHeader, TableHeader, EntityBlock, CardWithHeader,
        //InfiniteLoading
    },
    props: {
        corporation: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            members: [],
            page: 1,
            required_scopes: []
        }
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.$route('get.corporation.member_tracking', this.corporation.corporation_id), {
                params: {
                    page: this.page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.page += 1;
                    self.members.push(...response.data.data);
                    self.required_scopes =  response.data.required_scopes
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    },
}
</script>

<style scoped>

</style>
