<template>
    <Layout page="Create Scope Settings" active-sidebar-element="activeSidebarElement">
        <div>hello</div>
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
    </Layout>

</template>

<script>
    import Settings from "./Settings"
    import ScopeCheckbox from "./ScopeCheckbox"
    import SearchCorpOrAlliance from "./SearchCorpOrAlliance"
    import { Inertia } from '@inertiajs/inertia'
    import EveImage from "../../Shared/EveImage"
    import WideListElement from "../../Shared/WideListElement"
    import Layout from "../../Shared/Layout"

    export default {
        name: "ScopeSettings",
        components: {Layout, WideListElement, EveImage, SearchCorpOrAlliance, ScopeCheckbox, Settings},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entries: {
                type: Array,
                required: true
            },
            active: {
                type: Number,
                required: false
            }
        },
        data() {
            return {
                activeSidebarElement: route('server.settings'),
                selectedCorpOrAlliance: {},
                creating: true,
                open: false
            }
        },
        methods: {
            updateCharacterScopes: function (scopes) {
                this.selectedScopes.character = scopes
            },
            updateCorporationScopes: function (scopes) {
                this.selectedScopes.corporation = scopes
            },
            updateSelectedCorpOrAlliance: function (event) {
                this.selectedCorpOrAlliance = event
            },
            flatEntryScopes: function (entry) {

                let selected_scopes_array = _.filter(_.flatMap(entry.selectedScopes, function (scope) {
                    return _.split(scope, ',')
                }))

                return _.map(selected_scopes_array, scope => ({
                    id   : _.indexOf(selected_scopes_array, scope),
                    scope: scope
                }))
            },
            resetAndOpen: function () {

                this.selectedCorpOrAlliance = {}
                this.selectedScopes= {
                    character: [],
                    corporation: [],
                }

                this.creating = true

                this.open = true
            },
        },
        computed: {
            charFlavours: function () {
                return _.map(this.available_scopes.character, (value, prop) => ({
                    text: _.capitalize(prop),
                    value: _.toString(value)
                }));
            },
            corpFlavours: function () {
                let corpOptions = _.remove(this.available_scopes.corporation, function (value, prop) {
                        return prop === 'requirement'
                })

                return _.map(corpOptions, (value, prop) => ({
                    text: _.capitalize(prop),
                    value: _.toString(value)
                }));
            },
        },
        created() {

            if(_.isNumber(this.active)) {
                this.creating = false
                this.open = true

                let selectedElement = _.find(this.entries, {selectedEntity: {id: this.active}})

                this.selectedCorpOrAlliance = selectedElement.selectedEntity
                this.selectedScopes = selectedElement.selectedScopes
            }

        }
    }
</script>

<style scoped>

</style>
