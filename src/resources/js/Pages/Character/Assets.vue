<template>
    <Layout page="Character Assets" :required-scopes="this.requiredScopes">

        <div class="grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none mb-6">
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden col-span-2">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="grid grid-cols-6 gap-5">

                        <div class="col-span-6">
                            <label for="search" class="block text-sm font-medium leading-5 text-gray-700">Search</label>
                            <input v-model="search" id="search" class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <InputGroup for="character_dropdown" label="Character Filter">
                                <SeatPlusSelect v-model="character" id="character_dropdown">
                                    <option :value="null">All Characters</option>
                                    <option v-for="character in filters.owned_characters" :value="character.character_id" :key="character.character_id">{{ character.name }}</option>
                                </SeatPlusSelect>
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
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <DispatchUpdate :dispatchable_jobs="dispatchable_jobs" />
            </div>
        </div>


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

        <infinite-loading @infinite="loadAssets" spinner="waveDots" force-use-infinite-wrapper=".main.flex-1">
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
    export default {
        name: "Assets",
        components: {
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
            dispatchable_jobs: Object,
        },
        data() {
            return {
                requiredScopes: this.dispatchable_jobs.required_scopes,
                assets_data: [],
                page: 1,
                next_url: '',
                search: null,
                character: null,
                region: null,
            }
        },
        methods: {
            loadAssets($state) {
                const self = this;

                let data = {}

                for(let prop of ['character','region','search'])
                    if(self[prop])
                        data[prop] = self[prop]

                axios.post(this.$route('load.character.assets'), data, {
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
                this.assets_data = []
                this.page = 1
            }
        },
        computed: {
            selectedCharacterId : function () {
                return Number(this.buildSearchParams().get('character_id'));
            },
            selectedRegionId : function () {
                return Number(this.buildSearchParams().get('region_id'));
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
            }
        },
        watch: {
            search: function () {
                this.filter()
            },
            character() {
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
