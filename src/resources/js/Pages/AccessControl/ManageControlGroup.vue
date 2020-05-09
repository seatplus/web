<template>
    <Layout :page="'Edit ' + role.name" :active-sidebar-element="activeSidebarElement">
        <div class="container-fluid">
            <b-card no-body>
                <b-tabs v-model="tabIndex" pills card>
                    <b-tab title="Manual">
                        <b-card-text>
                            <b-card-title>Manual Control Group Member Management</b-card-title>
                            <b-card-text>
                                This is the very basic member setting. It means you need to manually add users to the role.
                            </b-card-text>
                        </b-card-text>
                    </b-tab>

                    <b-tab title="Automatic">
                        <b-card-title>TODO</b-card-title>
                        <b-card-text>
                            Quis magna Lorem anim amet ipsum do mollit sit cillum voluptate ex nulla tempor. Laborum
                            consequat non elit enim exercitation cillum aliqua consequat id aliqua. Esse ex
                            consectetur mollit voluptate est in duis laboris ad sit ipsum anim Lorem. Incididunt
                            veniam velit elit elit veniam Lorem aliqua quis ullamco deserunt sit enim elit aliqua
                            esse irure.
                        </b-card-text>
                    </b-tab>

                    <b-tab title="Opt-In">
                        <b-card-title>TODO</b-card-title>
                        <b-card-text>
                            Quis magna Lorem anim amet ipsum do mollit sit cillum voluptate ex nulla tempor. Laborum
                            consequat non elit enim exercitation cillum aliqua consequat id aliqua. Esse ex
                            consectetur mollit voluptate est in duis laboris ad sit ipsum anim Lorem. Incididunt
                            veniam velit elit elit veniam Lorem aliqua quis ullamco deserunt sit enim elit aliqua
                            esse irure.
                        </b-card-text>
                    </b-tab>

                    <b-tab title="Hidden">
                        <b-card-title>TODO</b-card-title>
                        <b-card-text>
                            Quis magna Lorem anim amet ipsum do mollit sit cillum voluptate ex nulla tempor. Laborum
                            consequat non elit enim exercitation cillum aliqua consequat id aliqua. Esse ex
                            consectetur mollit voluptate est in duis laboris ad sit ipsum anim Lorem. Incididunt
                            veniam velit elit elit veniam Lorem aliqua quis ullamco deserunt sit enim elit aliqua
                            esse irure.
                        </b-card-text>
                    </b-tab>
                </b-tabs>
            </b-card>

            <b-card>
                <b-card-title>Members</b-card-title>
                <b-card-text>
                    <multiselect v-model="selectedValues" :options="users" :multiple="true" :close-on-select="false" :clear-on-select="false" placeholder="Select users" label="name" track-by="id">
                        <template slot="tag" slot-scope="{ option, remove }">
                            <div class="table table-borderless">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <div class="p-2"><EveImage :object="option" :size="32" /></div>
                                            <div class="p-2 align-self-center">{{option.name}}</div>
                                        </div>
                                    </td>
                                    <td v-if="hasOtherCharacters" v-for="character in option.characters">
                                        <div class="d-flex flex-row">
                                            <div class="p-2"><EveImage :object="character" :size="32" /></div>
                                            <div class="p-2 align-self-center">{{character.name}}</div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </div>
                        </template>
                        <template slot="option" slot-scope="{option}">
                            <div class="table table-borderless">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <div class="p-2"><EveImage :object="option" :size="32" /></div>
                                            <div class="p-2 align-self-center">{{option.name}}</div>
                                        </div>
                                    </td>
                                    <td v-if="hasOtherCharacters" v-for="character in option.characters">
                                        <div class="d-flex flex-row">
                                            <div class="p-2"><EveImage :object="character" :size="32" /></div>
                                            <div class="p-2 align-self-center">{{character.name}}</div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </div>
                        </template>
                    </multiselect>
                </b-card-text>

                <inertia-link method="post" :href="$route('acl.manage.update',role.id)" :data="{selectedValues}" class="btn btn-success">Update</inertia-link>
            </b-card>

        </div>
    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import Multiselect from 'vue-multiselect'
    import EveImage from "../../Shared/EveImage"

    export default {
        name      : "ManageControlGroup",
        components: {Layout, Multiselect, EveImage},
        props: {
            role: {
                required: true
            },
            users: {
                type: Array,
                required: true
            },
            members: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                tabIndex: null,
                selectedValues: this.members
            }
        },
        methods: {
            hasOtherCharacters() {
                return ! _.isEmpty(option.characters)
            }
        },
        computed: {
            activeSidebarElement: function () {
                return route('acl.groups').url()
            }
        }
    }
</script>

<style scoped>

</style>
