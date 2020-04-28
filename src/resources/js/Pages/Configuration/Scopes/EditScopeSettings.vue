<template>
    <Layout :page="!this.entity ? 'Create ': 'Edit ' + 'Scope Settings'" :active-sidebar-element="$route('server.settings')">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Content goes here -->
                <SearchCorpOrAlliance v-if="!this.entity" @entities="setSelectedEntities" :error="this.$page.errors.selectedEntities"/>
                <h3 v-else class="text-lg leading-6 font-medium text-gray-900 inline-flex items-center">
                    <eve-image :size="128" tailwind_class="h-12 w12 rounded-full" :object="this.object" />
                    <span class="ml-4">{{ this.object.name }}</span>
                </h3>

                <div class="mt-6 sm:mt-5">
                    <div class="grid md:grid-cols-2 gap-4 sm:border-t sm:pt-2">
                        <div class="px-4 py-5">
                            <!-- Content goes here -->
                            <CharacterScopes
                                :scopes="this.available_scopes.character"
                                :selected-scopes="this.selectedCharacterScopes"
                                @scopes="selectedCharacterScopes = $event"
                            />
                        </div>
                        <div class="px-4 py-5">
                            <!-- Content goes here -->
                            <CorporationScopes
                                :scopes="this.available_scopes.corporation"
                                :selected-scopes="this.selectedCorporationScopes"
                                @corporation-scopes="selectedCorporationScopes = $event"/>
                        </div>
                    </div>
                    <p v-if=this.$page.errors.selectedScopes class="text-sm text-red-600">{{this.$page.errors.selectedScopes[0]}}</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-4 sm:px-6 text-right">
                <!-- Content goes here -->
                <span class="flex-1 flex justify-between">
                    <inertia-link :href="$route('delete.settings.scopes', entity.morphable_id)" method="delete" class="text-right inline-flex rounded-md shadow-sm justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out">
                        Delete
                    </inertia-link>

                    <inertia-link :href="$route('create.scopes')" method="post" :data ="{ selectedScopes: this.selectedScopes, selectedEntities: this.selectedEntities}" class="inline-flex justify-center rounded-md shadow-sm py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                        Save
                    </inertia-link>
                </span>
                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
            </div>
        </div>
    </Layout>

</template>

<script>
    import SearchCorpOrAlliance from "@/Pages/Configuration/SearchCorpOrAlliance"
    import Layout from "@/Shared/Layout"
    import CharacterScopes from "./CharacterScopes"
    import CorporationScopes from "./CorporationScopes"
    import Alerts from "@/Shared/Alerts"
    import EveImage from "@/Shared/EveImage"

    export default {
        name: "ScopeSettings",
        components: {EveImage, Alerts, CorporationScopes, CharacterScopes, Layout, SearchCorpOrAlliance},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entity: {
                type: Object,
                required: false,
                default: {}
            }
        },
        data() {
            return {
                selectedEntities: [],
                selectedCharacterScopes: [],
                selectedCorporationScopes: [],
            }
        },
        methods: {
            setSelectedEntities(newVal) {
                this.selectedEntities = newVal
            },
            post() {

                const url = this.route('create.scopes');

                const data = {
                    selectedScopes: this.selectedScopes,
                    selectedEntities: this.selectedEntities ?? {
                        id: 'id',
                        category: 'category',
                    }
                }

                return this.$inertia.post(url, data)
            },
            flatSelectedScopesFlavour(flavour, selectedScopes) {
                let flat_scopes = _.map(this.available_scopes[flavour], value => _.toString(value));

                return _.intersection(flat_scopes, selectedScopes)
            }

        },
        computed: {
            selectedScopes() {
                return _.union(this.selectedCharacterScopes, this.selectedCorporationScopes, this.available_scopes.minimum)
            },
            object() {
                const object =  {
                    name: this.entity.morphable.name
                }

                this.entity.morphable.corporation_id
                    ? object.corporation_id = this.entity.morphable.corporation_id
                    : object.alliance_id = this.entity.morphable.alliance_id

                return object
            },
        },
        watch: {
            entity(entity) {
                console.log(entity)
                if (_.isUndefined(entity.selected_scopes))
                    return

                this.selectedCorporationScopes = [1]

            },
            selectedCorporationScopes(newVal, oldVal) {
                if(oldVal.length === 0 && newVal.length > 0 && !_.includes(this.selectedCharacterScopes, 'esi-characters.read_corporation_roles.v1'))
                    this.selectedCharacterScopes.push('esi-characters.read_corporation_roles.v1')
            },
            selectedCharacterScopes(scopes) {
                if( !_.includes(scopes, 'esi-characters.read_corporation_roles.v1') && this.selectedCorporationScopes.length > 0)
                    this.$eventBus.$emit('role-removed')
            }
        },
        mounted() {
            if (_.isUndefined(this.entity.selected_scopes))
                return

            this.selectedCorporationScopes = this.flatSelectedScopesFlavour('corporation',this.entity.selected_scopes)
            this.selectedCharacterScopes = this.flatSelectedScopesFlavour('character',this.entity.selected_scopes)
            this.selectedEntities = [{
                id: this.entity.morphable_id,
                category: this.entity.morphable_type === "Seatplus\\Eveapi\\Models\\Corporation\\CorporationInfo" ? 'corporation' : 'alliance'
            }]
        }
    }
</script>

<style scoped>

</style>
