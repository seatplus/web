<template>
    <Layout page="Character Assets" :dispatch_transfer_object="dispatch_transfer_object">

        <template v-slot:title>
            <PageHeader>
                Character Assets
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver('update')">
                        Update
                    </HeaderButton>
                </template>
                <template v-slot:secondary>
                    <CharacterSelectionButton />
                </template>

            </PageHeader>
        </template>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-6 gap-5">

                    <div class="col-span-6 lg:col-span-2">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">Search</label>
                        <input v-model="params.search" id="search" class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                    </div>

                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
<!--                        <SelectComponent v-model="params.region" :options="regions">Region Filter</SelectComponent>-->
                        <Autosuggest v-model="params.regions" route="autosuggestion.region" label="Region" placeholder="search for region" />
                    </div>

                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <Autosuggest v-model="params.systems" route="autosuggestion.system" label="Solar System" placeholder="search for solar system" />
                    </div>

                </div>
            </div>
        </div>

        <AssetsComponent :params="params" @openManualLocationModal="openModal = true, modal_location_id = $event"/>

        <template v-slot:modal>
            <AddManualLocationModal v-model="openModal" :location_id="modal_location_id" />
        </template>

    </Layout>
</template>

<script>
import Layout from "../../Shared/Layout"
import DispatchUpdate from "../../Shared/DispatchUpdate"
import PageHeader from "../../Shared/Layout/PageHeader"
import HeaderButton from "../../Shared/Layout/HeaderButton"
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
import AddManualLocationModal from "../../Shared/Components/Assets/AddManualLocationModal";
import Autosuggest from "@/Shared/Components/Autosuggest";
import LocationComponent from "@/Shared/Components/Assets/LocationComponent";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";

export default {
    name: "Assets",
    components: {
        AssetsComponent,
        LocationComponent,
        Autosuggest,
        AddManualLocationModal,
        CharacterSelectionButton,
        HeaderButton,
        PageHeader,
        DispatchUpdate,
        Layout
    },
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            params: {
                page: 1,
                character_ids: this.selectedCharacterIds,
                search: null,
                regions: null,
                systems: null,
            },
            openModal: false,
            modal_location_id: 0
        }
    },
    methods: {
        openSlideOver(value) {
            this.$eventBus.$emit('open-slideOver', value);
        },
    },
    computed: {
        computedParams() {
            return this.params
        }
    }
}
</script>

<style scoped>

</style>
