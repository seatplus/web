<template>
    <Layout page-header="Character" page-description="Assets">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <b-form-group label="Character Filter">
                                        <character-dropdown :owned="$page.filters.owned_characters" @selectedCharacterId="onCharacterSelection" :character-id="selectedCharacterId"/>
                                    </b-form-group>
                                </div>
                                <div class="col-md-6">
                                    <b-form-group label="Region Filter">
                                        <region-dropdown :regions="$page.filters.regions" @selectedRegionId="onRegionSelection" :region-id="selectedRegionId" />
                                    </b-form-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <b-form-group label="Character Filter">
                                        <b-form-input v-model="search" placeholder="Enter your name" />
                                    </b-form-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="card" v-for="location_assets in this.getGroupedAssets()">
                        <div class="card-header">
                            <h3 v-if="location_assets[0]['location']" class="card-title">
                                {{location_assets[0]['location']['locatable']['name']}}
                            </h3>
                            <h3 v-else class="card-title">
                                Unknown Structure ({{location_assets[0]['location_id']}})
                            </h3>
                            <span class="float-right">
                                  {{getLocationsVolume(location_assets)}} volume and {{getLoationsItemsCount(location_assets)}} items
                              </span>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <table class="table table-responsive-md table-sm">
                                <thead class="thead-light">
                                <tr>
                                    <th>Quantity</th>
                                    <th>Type</th>
                                    <th>Volume</th>
                                    <th>Group</th>
                                </tr>
                                </thead>

                                <tbody v-for="asset in location_assets">
                                <tr>
                                    <td style="width: 5%">
                                        <div v-if="asset.content[0]">
                                            <asset-button :asset="asset" />
                                        </div>
                                        <div v-else>
                                            {{ asset.quantity }}
                                        </div>

                                    </td>
                                    <td style="width: 55%">
                                        <b-media vertical-align="center">
                                            <template v-slot:aside>
                                                <EveImage v-if="multipleCharacters()" :object="asset.owner" :size=32 />
                                                <EveImage :object="asset.type" :size=32 />
                                            </template>
                                            <span v-if="asset.name">
                                              {{asset.name}} ({{ asset.type.name }})
                                          </span>

                                            <span v-else>
                                              {{ asset.type.name }}
                                          </span>

                                            <span v-if="!asset.is_singleton" class="text-info">(packaged)</span>
                                        </b-media>

                                    </td>
                                    <td style="width: 20%">{{getMetricPrefix(asset.quantity * asset.type.volume)}}</td>
                                    <td style="width: 20%">{{asset.type.group.name}}</td>
                                </tr>

                                <b-collapse v-if="asset.content[0]"
                                            v-for="content in asset.content" :key="asset.content.item_id"
                                            :id="content.location_id.toString()" tag="tr" style="background-color: #f2f2f2">

                                    <td>
                                        <div v-if="content.content[0]">
                                            <b-button variant="link" size="sm" v-b-toggle="content.content[0].location_id">
                                                <i class="fas fa-plus"/>
                                            </b-button>
                                        </div>
                                        <div v-else>
                                            {{ content.quantity }}
                                        </div>

                                    </td>
                                    <td>
                                        <b-media vertical-align="center">
                                            <template v-slot:aside>
                                                <eve-image v-if="$page.numer_of_owner > 1" :object="content.owner" :size=32 />
                                                <EveImage :object="content.type" :size=32 />
                                            </template>
                                            <span v-if="content.name">
                                              {{content.name}} ({{ content.type.name }})
                                          </span>

                                            <span v-else>
                                              {{ content.type.name }}
                                          </span>
                                        </b-media>

                                    </td>
                                    <td>{{getMetricPrefix(content.quantity * content.type.volume)}}</td>
                                    <td>{{content.type.group.name}}</td>

                                </b-collapse>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>

                    <pagination :collection="assets"/>

                </div>
            </div>
        </div>

    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import EveImage from "../../Shared/EveImage"
    import AssetButton from "./AssetButton"
    import Pagination from "../../Shared/Pagination"
    import CharacterDropdown from "../../Shared/CharacterDropdown"
    import {Inertia} from '@inertiajs/inertia'
    import RegionDropdown from "../../Shared/RegionDropdown"

    export default {
        name: "Assets",
        components: {Layout, EveImage, AssetButton, Pagination, CharacterDropdown, RegionDropdown},
        props: {
            assets: Object,
        },
        data() {
            return {
                search: this.buildSearchParams().get('search_param'),
                last_page: this.assets.meta.last_page
            }
        },
        methods: {
            getGroupedAssets() {

                return _.groupBy(this.assets.data, 'location_id')
            },
            getMetricPrefix(numeric_value) {

                const  { prefix } = require('metric-prefix')

                return prefix(numeric_value, { precision: 3, unit: 'm³'})
            },
            getLocationsVolume(location_assets) {

                function volume(object) {
                    return object.quantity * object.type.volume;
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
