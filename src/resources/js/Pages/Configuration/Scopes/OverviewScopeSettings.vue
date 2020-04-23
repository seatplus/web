<template>
    <Settings :layout-object="this.layoutObject">
        <ul>
            <WideListElement
                v-for="(entry,index) in this.entries"
                :key="entry.selectedEntity.id"
                :url="route('edit.scopes.settings', entry.selectedEntity.id)"
                :class="{'border-t border-gray-200': index >0}"
            >
                <template v-slot:avatar>
                    <eve-image :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'" :object="entry.selectedEntity" :size="128"/>
                </template>
                <template v-slot:upper_left>{{entry.selectedEntity.name}}</template>
                <template slot="lower_right">

                </template>
                <template slot="navigation">
                    <svg class="text-gray-400 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
            </WideListElement>
            <li>
                <inertia-link :href="this.route('view.create.scopes')" :class="['block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out', {'border-t border-gray-200': this.entries.length > 0 }]">
                    <div class="flex items-center px-4 py-4 sm:px-6">
                        <div class="min-w-0 flex-1 flex items-center">
                            <div class="flex overflow-x-visible">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8"><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="px-4 capitalize">
                                create
                            </div>
                        </div>
                        <div>
                            <svg class="text-gray-400 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </inertia-link>
            </li>
        </ul>
        <!--<table class="table table-hover">
            <tbody>
            <tr v-for="entry in this.entries">
                <td><eve-image :object="entry.selectedEntity" :size="32" /></td>
                <td>{{entry.selectedEntity.name}}</td>
                <td>
                    <b-badge v-for="scope in flatEntryScopes(entry)" variant="info" :key="scope.id" class="mr-1">{{scope.scope}} </b-badge>
                </td>
                <td>
                    <div class="btn-group">
                        <inertia-link class="btn btn-primary" :href="route('settings.scopes',entry.selectedEntity.id)">Update</inertia-link>
                        <inertia-link class="btn btn-danger" method="delete" :href="route('delete.settings.scopes',entry.selectedEntity.id)">Delete</inertia-link>
                    </div>

                </td>

            </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            <b-button @click="resetAndOpen" >Create scope settings</b-button>

            <b-modal v-model="open" :title="selectedCorpOrAlliance ? selectedCorpOrAlliance.name + ' Scope Settings' : 'Scope Settings'" size="lg" scrollable>
                <div class=" my-4 d-flex justify-content-around">
                    <div class="flex-fill">
                        <scope-checkbox :flavours="this.charFlavours" type="Character"
                                        @selected-scopes="updateCharacterScopes"
                                        :selected-flavours="this.selectedScopes.character"/>
                    </div>
                    <div class="flex-fill">
                        <scope-checkbox :flavours="this.corpFlavours" type="Corporation"
                                        @selected-scopes="updateCorporationScopes"
                                        :selected-flavours="this.selectedScopes.corporation"/>
                    </div>
                </div>
                <b-form-group v-if="creating" label="Search the corporation or alliance that the scope should be applied for" description="you might need to delete a sign to see the found results" >
                    <search-corp-or-alliance @selected-corp-or-alliance="updateSelectedCorpOrAlliance"/>
                </b-form-group>

                <template v-slot:modal-footer="{ ok, cancel}">

                    <inertia-link method="post" class="btn btn-primary" @click="ok()" :href="route('updateOrCreate.settings.scopes')" :data="{selectedScopes, selectedCorpOrAlliance}">
                        Create
                    </inertia-link>
                </template>

            </b-modal>
        </div>-->
    </Settings>

</template>

<script>
    import Settings from "@/Pages/Configuration/Settings"
    import WideListElement from "@/Shared/WideListElement"
    import EveImage from "@/Shared/EveImage"

    export default {
        name: "OverviewScopeSettings",
        components: {WideListElement, EveImage, Settings},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entries: {
                type: Array,
                required: true
            },
        },
        data() {
            return {
                layoutObject: {
                    pageHeader: 'Server Settings',
                    pageDescription: 'Scope',
                    activeSidebarElement: route('server.settings')
                },
            }
        },
        methods: {

        },
        computed: {

        },
    }
</script>

<style scoped>

</style>
