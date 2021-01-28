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

                    <div class="col-span-6 md:col-span-4">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">Search</label>
                        <input v-model="search" id="search" class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <SelectComponent v-model="region" :options="regions">Region Filter</SelectComponent>
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
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
import SelectComponent from "../../Shared/Components/SelectComponent";

export default {
        name: "Assets",
        components: {
            SelectComponent,
            CharacterSelectionButton,
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
                infiniteId: +new Date(),
                assets_data: [],
                page: 1,
                search: null,
                region: null,
                regions: []
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

                this.assets_data = [];
                this.page = 1;
                this.infiniteId += 1;
            },
            openSlideOver(value) {
                this.$eventBus.$emit('open-slideOver', value);
            },
        },
        computed: {
            selectedCharacterIds() {

                let character_ids = _.get(this.$route().params, 'character_ids')

                if(!character_ids)
                    return []

                return  _.map(character_ids, (id) => parseInt(id))
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
        created() {

                this.regions = _.map(this.filters.regions, (region) => {

                    return {
                        value: region.region_id,
                        text: region.name
                    }
                })

        }
    }
</script>

<style scoped>

</style>
