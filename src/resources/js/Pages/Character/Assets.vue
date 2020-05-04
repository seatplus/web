<template>
    <Layout page="Character Assets" :required-scopes="this.requiredScopes">

        <div class="grid gap-2 grid-cols-3">
            <div class="col-span-2">

            </div>
            <div class="col-span-1">
                <!--<div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        &lt;!&ndash; Content goes here &ndash;&gt;
                    </div>
                </div>-->
            </div>
        </div>

        <div class="bg-white overflow-hidden overflow-hidden mb-3 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Content goes here -->
                <div class="grid grid-cols-6 gap-6">

                    <div class="col-span-6">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">Search</label>
                        <input v-model="search" id="search" class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <character-dropdown @selectedCharacterId="onCharacterSelection" :character-id="selectedCharacterId"/>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <region-dropdown :regions="filters.regions" @selectedRegionId="onRegionSelection" :region-id="selectedRegionId" />
                    </div>

                </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
                <!-- Content goes here -->
                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
                <pagination :collection="assets"/>
            </div>
        </div>

        <wide-lists v-for="location in this.groupedAssets" :key="location.location_id">
            <template v-slot:header>
                <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ location.location }}
                    </h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        {{getLocationsVolume(location.assets)}} volume and {{getLoationsItemsCount(location.assets)}} items
                    </p>
                </div>
            </template>
            <template v-slot:elements>
                <wide-list-element v-for="asset in location.assets" :key="asset.item_id" :url="url(asset)" >
                    <template v-slot:avatar>
                        <span class="inline-block relative">
                            <eve-image :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'" :object="asset.type" :size="128"/>
                            <span v-if="asset.quantity > 1" class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ asset.quantity }}</span>
                            </span>
                        </span>
                        <EveImage v-if="multipleCharacters()" :tailwind_class="'-ml-1 inline-block h-12 w-12 rounded-full text-white shadow-solid'" :object="asset.owner" :size="128" />
                    </template>

                    <template slot="upper_left">{{asset.name}}</template>

                    <template slot="lower_left">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>

                        <span class="truncate">{{ asset.type.name }}</span>
                    </template>

                    <template slot="upper_right">{{asset.type.group.name}} <span v-if="!asset.is_singleton" class="text-info">(packaged)</span></template>

                    <template slot="lower_right">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"></path>
                        </svg>
                        {{getMetricPrefix(asset.quantity * asset.type.volume)}}
                    </template>

                    <template slot="navigation">
                        <inertia-link :href="$route('character.item', asset.item_id)" >
                            <svg :class="[{'text-gray-400' : asset.content[0], 'text-transparent' : !asset.content[0]},'h-5 w-5']" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </inertia-link>

                    </template>

                </wide-list-element>
            </template>
        </wide-lists>

    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import EveImage from "../../Shared/EveImage"
    import Pagination from "../../Shared/Pagination"
    import CharacterDropdown from "../../Shared/CharacterDropdown"
    import {Inertia} from '@inertiajs/inertia'
    import RegionDropdown from "../../Shared/RegionDropdown"
    import WideLists from "../../Shared/WideLists"
    import WideListElement from "../../Shared/WideListElement"

    export default {
        name: "Assets",
        components: {
            WideListElement,
            WideLists, Layout, EveImage, Pagination, CharacterDropdown, RegionDropdown},
        props: {
            assets: Object,
            filters: Object,
        },
        data() {
            return {
                search: this.buildSearchParams().get('search_param'),
                last_page: this.assets.meta.last_page,
                requiredScopes: ['esi-assets.read_assets.v1',  'esi-universe.read_structures.v1']
            }
        },
        methods: {
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
            getLoationsItemsCount(location_assets) {

                return _.size(location_assets)
            },
            onCharacterSelection: function (character_id) {

                let searchParams = this.buildSearchParams();

                if(searchParams.has('character_id') && character_id == null)
                    searchParams.delete('character_id')

                if(character_id)
                    searchParams.set("character_id", character_id);

                let url = window.location.href.split('?')[0] + '?' + searchParams.toString();

                Inertia.visit(url, {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['assets'],
                })
            },
            buildSearchParams: function () {
                return new URL(window.location.href).searchParams;
            },
            multipleCharacters : function () {
                return !this.buildSearchParams().has('character_id');
            },
            onRegionSelection: function (region_id) {

                let searchParams = this.buildSearchParams();

                if(searchParams.has('region_id') && region_id == null)
                    searchParams.delete('region_id')

                if(region_id)
                    searchParams.set("region_id", region_id);

                let url = window.location.href.split('?')[0] + '?' + searchParams.toString();

                Inertia.visit(url, {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['assets'],
                })
            },
            url(asset) {
                return asset.content[0] ? this.$route('character.item', asset.item_id) : '#'
            }
        },
        updated: function() {

            if(this.assets.meta.last_page < this.assets.meta.current_page) {
                let searchParams = this.buildSearchParams();

                searchParams.set("page", this.assets.meta.last_page);

                if(this.assets.meta.last_page === 1)
                    searchParams.delete('page')

                let url = window.location.href.split('?')[0] + '?' + searchParams.toString();

                Inertia.visit(url, {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['assets'],
                })
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
                return  _.map(_.groupBy(this.assets.data, 'location_id'), (value, prop) => (
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
                let searchParams = this.buildSearchParams();

                if(searchParams.has('search_param') && this.search === '')
                    searchParams.delete('search_param')

                if(this.search)
                    searchParams.set("search_param", this.search);

                let url = window.location.href.split('?')[0] + '?' + searchParams.toString();

                Inertia.visit(url, {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['assets'],
                })
            },
        }
    }
</script>

<style scoped>

</style>
