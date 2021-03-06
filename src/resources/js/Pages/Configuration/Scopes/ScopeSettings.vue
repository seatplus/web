<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>


    <PageHeader>
      {{creationMode ? 'Create ': 'Edit '}} Scope Setting
      <template v-slot:primary>
        <inertia-link :href="$route('create.scopes')" method="post" as="button" :data ="{selectedScopes: selected_scopes, selectedEntities: selectedEntities, type: type}" class="inline-flex justify-center rounded-md shadow-sm py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
          Save
        </inertia-link>
      </template>
      <template v-slot:secondary v-if="!creationMode">
        <span class="shadow-sm rounded-md">
            <inertia-link :href="$route('delete.scopes', object.id)" method="delete" as="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out">
                Deletes
            </inertia-link>
        </span>
      </template>
    </PageHeader>


    <div class="grid gap-6 grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="bg-white overflow-hidden shadow rounded-lg col-span-2">
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
            <eve-image :size="128" tailwind_class="h-12 w-12 rounded-full" :object="object" />
            <span class="ml-4">{{ object.name }}</span>
          </h3>

          <div class="mt-6 sm:mt-5">
            <div class="grid md:grid-cols-2 gap-4 sm:border-t sm:pt-2">
              <div class="px-4 py-5">
                <!-- Content goes here -->
                <CharacterScopes
                  :scopes="available_scopes.character"
                  v-model:selected-scopes="selected_scopes"
                  :key="`character ${scopesAsString}`"
                />
              </div>
              <div class="px-4 py-5">
                <!-- Content goes here -->
                <CorporationScopes
                  :scopes="available_scopes.corporation"
                  v-model:selected-scopes="selected_scopes"
                  :key="`corporation ${scopesAsString}`"
                />
              </div>
            </div>
            <p v-if=selectedScopesError class="text-sm text-red-600"> {{ selectedScopesError }} </p>
            <p v-if=selectedEntitiesError class="text-sm text-red-600">{{ selectedEntitiesError }}</p>
          </div>
        </div>
      </div>
      <div class="col-span-3 md:col-span-2 lg:col-span-1">
        <RadioListWithDescription v-model="selectedModula" :options="options" :title="'Scope'" class="overflow-hidden shadow rounded-lg"/>
      </div>

    </div>

  </div>

</template>

<script>

import Layout from "@/Shared/SidebarLayout/Layout"
import CharacterScopes from "./CharacterScopes"
import CorporationScopes from "./CorporationScopes"
import EveImage from "@/Shared/EveImage"
import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance"
import PageHeader from "@/Shared/Layout/PageHeader"
import RadioListWithDescription from "@/Shared/Layout/RadioListWithDescription";

export default {
    name: "ScopeSettings",
    components: {
        RadioListWithDescription,
        PageHeader, SearchCorpOrAlliance, EveImage, CorporationScopes, CharacterScopes, Layout},
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
        options: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            selectedEntities: [],
            selected_scopes: _.toArray(_.get(this.entity, 'selected_scopes', {})),
            creationMode: route().current() === 'view.create.scopes',
            selectedModula: 0
        }
    },
    methods: {
        post() {

            const url = this.route('create.scopes');

            const data = {
                selectedScopes: this.selectedScopes,
                selectedEntities: this.selectedEntities != null ? this.selectedEntities : {
                    id: 'id',
                    category: 'category',
                }
            }

            return this.$inertia.post(url, data)
        }
    },
    computed: {
        object() {

            if(_.isUndefined(this.entity.morphable))
                return {}

            const object =  {
                name: this.entity.morphable.name,
                id: this.entity.morphable_id
            }

            this.entity.morphable.corporation_id
                ? object.corporation_id = this.entity.morphable.corporation_id
                : object.alliance_id = this.entity.morphable.alliance_id

            return object
        },
        type() {
            return this.options[this.selectedModula].title
        },
        isGlobal() {
            return this.options[this.selectedModula].title === 'global';
        },
        selectedScopesError() {
            return _.get(this.$page, 'props.errors.selectedScopes[0]')
        },
        selectedEntitiesError() {
            return _.get(this.$page, 'props.errors.selectedEntities[0]')
        },
        scopesAsString() {
            return _.toString(this.selected_scopes)
        },
        pageTitle() {

            let mode = this.creationMode ? 'Create' : 'Edit'

            return `${mode} Scope Settings`
        },
    },
    mounted() {
        if (_.isUndefined(this.entity.selected_scopes))
            return

        this.selectedEntities = [{
            id: this.entity.morphable_id,
            category: this.entity.morphable_type === "Seatplus\\Eveapi\\Models\\Corporation\\CorporationInfo" ? 'corporation' : 'alliance'
        }]
    },
    created: function () {
        this.selectedModula = this.creationMode ? 0 : _.findIndex(this.options, {'title': this.entity?.type});
    }
}
</script>

<style scoped>

</style>
