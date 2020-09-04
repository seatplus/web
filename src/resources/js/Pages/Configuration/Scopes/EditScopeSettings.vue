<template>
    <Layout :page="(creationMode ? 'Create ': 'Edit ') + 'Scope Settings'" :active-sidebar-element="$route('server.settings')">

        <template v-slot:title>
            <PageHeader>
                {{creationMode ? 'Create ': 'Edit '}} Scope Settings
                <template v-slot:primary v-if="creationMode">
                    Global Permissions
                    <div class="flex items-center justify-center ml-4">
                        <span role="checkbox" tabindex="0" v-on:click="isGlobal = !isGlobal" @keydown.space.prevent="isGlobal = !isGlobal" :aria-checked="isGlobal.toString()" aria-checked="false" :class="{ 'bg-gray-200': !isGlobal, 'bg-indigo-600': isGlobal }" class="bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                            <span aria-hidden="true" :class="{ 'translate-x-5': isGlobal, 'translate-x-0': !isGlobal }" class="translate-x-0 inline-block h-5 w-5 rounded-full bg-white shadow transform transition ease-in-out duration-200"></span>
                        </span>
                    </div>
                </template>
            </PageHeader>
        </template>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Content goes here -->
                <SearchCorpOrAlliance v-if="creationMode && !isGlobal" v-model=selectedEntities />
                <h3 v-else-if="isGlobal" class="text-lg leading-6 font-medium text-gray-900 inline-flex items-center">
                    <!--<eve-image :size="128" tailwind_class="h-12 w12 rounded-full" :object="this.object" />-->
                    <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="ml-4">Global Permission</span>
                </h3>
                <h3 v-else class="text-lg leading-6 font-medium text-gray-900 inline-flex items-center">
                    <eve-image :size="128" tailwind_class="h-12 w-12 rounded-full" :object="this.object" />
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
                    <inertia-link v-if="this.entity.morphable_id" :href="$route('delete.settings.scopes', entity.morphable_id)" method="delete" class="text-right inline-flex rounded-md shadow-sm justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out">
                        Delete
                    </inertia-link>

                    <inertia-link v-if="isGlobal && !creationMode" :href="$route('delete.global.scopes')" method="delete" class="text-right inline-flex rounded-md shadow-sm justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out">
                        Delete
                    </inertia-link>

                    <inertia-link v-if="isGlobal" :href="$route('create.global.scopes')" method="post" :data ="{ selectedScopes: this.selectedScopes }" class="inline-flex justify-center rounded-md shadow-sm py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                        Save
                    </inertia-link>

                    <inertia-link v-else :href="$route('create.scopes')" method="post" :data ="{ selectedScopes: this.selectedScopes, selectedEntities: this.selectedEntities}" class="inline-flex justify-center rounded-md shadow-sm py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                        Save
                    </inertia-link>
                </span>
                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
            </div>
        </div>
    </Layout>

</template>

<script>

    import Layout from "@/Shared/Layout"
    import CharacterScopes from "./CharacterScopes"
    import CorporationScopes from "./CorporationScopes"
    import Alerts from "@/Shared/Alerts"
    import EveImage from "@/Shared/EveImage"
    import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance"
    import PageHeader from "@/Shared/Layout/PageHeader"

    export default {
        name: "ScopeSettings",
        components: {PageHeader, SearchCorpOrAlliance, EveImage, Alerts, CorporationScopes, CharacterScopes, Layout},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entity: {
                type: Object,
                required: false,
                default: function () {return {}}
            },
            hasGlobalScopes: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                selectedEntities: [],
                selectedCharacterScopes: [],
                selectedCorporationScopes: [],
                isGlobal: false
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
            creationMode() {
                return _.isEmpty(this.entity);
            },
            object() {

                if(_.isUndefined(this.entity.morphable))
                    return {}

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

            this.isGlobal = this.hasGlobalScopes
        }
    }
</script>

<style scoped>

</style>
