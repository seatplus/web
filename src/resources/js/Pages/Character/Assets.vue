<template>
    <Layout page="Character Assets" :required-scopes="dispatch_transfer_object.required_scopes">

        <template v-slot:title>
            <PageHeader>
                Character Assets
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Update
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-6 gap-5">

                    <div class="col-span-6">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">Search</label>
                        <input v-model="search" id="search" class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <InputGroup for="character_dropdown" label="Character Filter">
                            <span class="mt-1 inline-flex rounded-md shadow-sm box w-full">
                                <button @click="characterFilterModal.open = true" type="button" class="inline-flex justify-between items-center box w-full pl-3 pr-2 py-2 border border-gray-300 text-base leading-6 rounded-md focus:outline-none focus:ring-gray transition ease-in-out duration-150">
                                    {{ characterFilterModal.selectedCharacters.length === 0 ? 'Own Characters' : (characterFilterModal.selectedCharacters.length === 1 ? '1 Character' : characterFilterModal.selectedCharacters.length + ' Characters') }}
                                    <svg class="h-6 w-6 float-right" viewBox="0 0 20 20" fill="none">
                                        <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke="#9fa6b2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </span>
                        </InputGroup>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <InputGroup for="region_dropdown" label="Region Filter">
                            <SeatPlusSelect v-model="region" id="region_dropdown">
                                <option :value="null">All Regions</option>
                                <option v-for="region in filters.regions" :value="region.region_id" :key="region.region_id">{{ region.name }}</option>
                            </SeatPlusSelect>
                        </InputGroup>
                    </div>

                </div>
            </div>
        </div>

        <div class="space-y-2 sm:space-y-6">
            <wide-lists v-for="location in groupedAssets" :key="location.location_id">
                <template v-slot:header>
                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ location.location }}
                        </h3>
                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            {{getLocationsVolume(location.assets)}} volume and {{getLocationsItemsCount(location.assets)}}
                            items
                        </p>
                    </div>
                </template>
                <template v-slot:elements>
                    <ItemList :items="location.assets"></ItemList>
                </template>
            </wide-lists>
        </div>


        <infinite-loading :identifier="infiniteId" @infinite="loadAssets" spinner="waveDots" force-use-infinite-wrapper=".main.flex-1">
            <div slot="no-more">all loaded</div>
        </infinite-loading>

        <template v-slot:modal>
            <CharacterFilterModal permission="character.assets" v-model="characterFilterModal" />
        </template>

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
                <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
            </SlideOver>
        </template>

    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import EveImage from "../../Shared/EveImage"
    import Pagination from "../../Shared/Pagination"
    import WideLists from "../../Shared/WideLists"
    import WideListElement from "../../Shared/WideListElement"
    import DispatchUpdate from "../../Shared/DispatchUpdate"
    import ItemList from "./ItemList"
    import InputGroup from "../../Shared/InputGroup"
    import SeatPlusSelect from "../../Shared/SeatPlusSelect"
    import InfiniteLoading from "vue-infinite-loading"
    import ModalWithFooter from "../../Shared/Modals/ModalWithFooter"
    import Modal from "../../Shared/Modals/Modal"
    import CharacterFilterModal from "@/Shared/Modals/CharacterFilterModal"
    import SlideOver from "../../Shared/Layout/SlideOver"
    import PageHeader from "../../Shared/Layout/PageHeader"
    import HeaderButton from "../../Shared/Layout/HeaderButton"
    export default {
        name: "Assets",
        components: {
            HeaderButton,
            PageHeader,
            SlideOver,
            CharacterFilterModal,
            Modal,
            ModalWithFooter,
            SeatPlusSelect,
            InputGroup,
            ItemList,
            DispatchUpdate,
            WideListElement,
            InfiniteLoading,
            WideLists, Layout, EveImage, Pagination
        },
        props: {
            filters: Object,
            dispatch_transfer_object: {
                required: true,
                type: Object
            },
        },
        data() {
            return {
                requiredScopes: ['esi-assets.read_assets.v1', 'esi-universe.read_structures.v1'],
                infiniteId: +new Date(),
                assets_data: [],
                page: 1,
                search: null,
                region: null,
                characterFilterModal: {
                    open: false,
                    selectedCharacters: []
                }
            }
        },
        methods: {
            loadAssets($state) {
                const self = this;

                axios.get(this.$route('load.character.assets'), {
                    params: {
                        page: this.page,
                        character_ids: this.selectedCharacterIds,
                        search: this.search,
                        region: this.region
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
            getMetricPrefix(numeric_value) {

                const  { prefix } = require('metric-prefix')

                return prefix(numeric_value, { precision: 3, unit: 'm³'})
            },
            getLocationsVolume(location_assets) {

                function volume(object) {
                    return object.type.volume ? object.quantity * object.type.volume : 0;
                }

                const  { prefix } = require('metric-prefix')

                return prefix(_.sum(_.map(location_assets,volume)), { precision: 3, unit: 'm³'})
            },
            getLocationsItemsCount(location_assets) {

                return _.size(location_assets)
            },
            filter() {

                if(_.isEmpty(this.selectedCharacterIds))
                    return

                this.assets_data = [];
                this.page = 1;
                this.infiniteId += 1;
            },
            openSlideOver() {
                this.$eventBus.$emit('open-slideOver');
            }
        },
        computed: {
            selectedCharacterIds() {
                return this.characterFilterModal.selectedCharacters
            },
            groupedAssets() {

                return  _.map(_.groupBy(this.assets_data, 'location_id'), (value, prop) => (
                    {
                        location_id: _.toInteger(prop),
                        location: value[0].location ? value[0].location.locatable.name : 'Unknown Structure (' + _.toInteger(prop) +')' ,
                        assets: _.map(value, function(asset) {

                            asset.type =  asset.type ?? { type_id: asset.type_id, name: '', group: { name: '' }}
                            asset.type.group = asset.type.group ?? { name: '' }

                            return asset
                        }),
                    }
                ))
            },
        },
        watch: {
            search: function () {
                this.filter()
            },
            selectedCharacterIds() {
                this.filter()
            },
            region() {
                this.filter()
            }
        },
    }
</script>

<style scoped>

</style>
