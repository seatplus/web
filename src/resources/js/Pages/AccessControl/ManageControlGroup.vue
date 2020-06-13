<template>
    <Layout :active-sidebar-element="activeSidebarElement">
        <template v-slot:title>
            <PageHeader :breadcrumbs="[{name: 'Control Group', route: 'acl.groups'}]">
                Edit {{ role.name }}
                <template v-slot:primary>
                    <HeaderButton @click="store">
                        Save
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div>
                <div class="sm:hidden">
                    <select aria-label="Selected tab" class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                        <option>My Account
                        </option>
                        <option>Company
                        </option>
                        <option selected>Team Members
                        </option>
                        <option>Billing
                        </option>
                    </select>
                </div>
                <div class="hidden sm:block">
                    <div class="px-4 sm:px-6 border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <a href="#" class="whitespace-no-wrap  py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm leading-5 text-indigo-600 focus:outline-none focus:text-indigo-800 focus:border-indigo-700" aria-current="page">
                                Manual
                            </a>
                            <a href="#" class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                Automatic
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-blue-100 text-blue-800">
                                    Coming soon
                                </span>
                            </a>
                            <a href="#" class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                On Request
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-blue-100 text-blue-800">
                                    Coming soon
                                </span>
                            </a>
                            <a href="#" class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                Secret
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-blue-100 text-blue-800">
                                    Coming soon
                                </span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <p class="m-3 text-base leading-6 text-gray-500">
                    This is the very basic member setting. It means you need to manually add users to the role.
                </p>

                <div class="mb-3 grid gap-5 max-w-lg mx-auto lg:grid-cols-2 lg:max-w-none">
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white flex flex-col justify-between">
                            <div class="flex-1 p-6 ">
                                <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                    Available Users
                                </h3>
                                <p class="mt-3 text-base leading-6 text-gray-500">
                                    Assign below any user to the control group.
                                </p>
                            </div>

                            <div :class="['bg-white sm:rounded-md', {'shadow' : !isEmpty(filteredUsers)}]">
                                <ListTransition :entries="filteredUsers" :class="'divide-y divide-grey-200'">
                                    <div v-for="(entity, index) of filteredUsers" :key="index" class="px-4 py-4 flex items-center justify-between sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                                        <EveImage v-if="entity" :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />

                                        <button @click="addSelectedUser(entity)" :class="['text-green-500 hover:bg-green-100 focus:bg-green-100','inline-flex rounded-md p-1.5 focus:outline-none transition ease-in-out duration-150']">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path  fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </ListTransition>
                            </div>
                            <!--<AffiliationList v-model="allowed" />-->
                        </div>
                    </div>
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white flex flex-col justify-between">
                            <div class="flex-1 p-6 ">
                                <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                    Selected Users
                                </h3>
                                <p class="mt-3 text-base leading-6 text-gray-500">
                                    Remove below any user from the Control Group
                                </p>
                            </div>
                            <AffiliationList v-model="selectedUsers" />
                            <!--<AffiliationList v-model="inverse" />-->
                        </div>
                    </div>
                    <!--<div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white flex flex-col justify-between">
                            <div class="flex-1 p-6 ">
                                <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                    Forbidden
                                </h3>
                                <p class="mt-3 text-base leading-6 text-gray-500">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                                </p>
                            </div>
                            <AffiliationList v-model="forbidden" />
                        </div>
                    </div>-->
                </div>
                <!--<multiselect v-model="selectedValues" :options="users" :multiple="true" :close-on-select="false" :clear-on-select="false" placeholder="Select users" label="name" track-by="id">
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
                </multiselect>-->
            </div>
        </div>



    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import Multiselect from 'vue-multiselect'
    import EveImage from "../../Shared/EveImage"
    import PageHeader from "../../Shared/Layout/PageHeader"
    import HeaderButton from "../../Shared/Layout/HeaderButton"
    import ListTransition from "../../Shared/Transitions/ListTransition"
    import AffiliationList from "./AffiliationList"
    import {Inertia} from "@inertiajs/inertia"

    export default {
        name      : "ManageControlGroup",
        components: {AffiliationList, ListTransition, HeaderButton, PageHeader, Layout, Multiselect, EveImage},
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
                availableUsers: [],
                selectedUsers: this.members,
                search: null
            }
        },
        methods: {
            hasOtherCharacters() {
                return ! _.isEmpty(option.characters)
            },
            isEmpty(array) {
                return _.isEmpty(array)
            },
            removeEntity(entity) {
                this.list = _.filter(this.list, (entry) => entry !== entity)
            },
            sortByName(entities) {
                return _.sortBy(entities, (entity) => entity.name)
            },
            addSelectedUser(entity) {
                this.selectedUsers.unshift(entity)
            },
            store: function () {

                let data = {
                    selectedValues: this.selectedUsers
                }

                Inertia.post(window.location.href, data, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            }
        },
        computed: {
            activeSidebarElement: function () {
                return route('acl.groups').url()
            },
            filteredUsers() {

                let entities = _.filter(this.users, (entity) => !_.map(this.selectedUsers, _.property('id')).includes(entity.id))

                if(_.isNull(this.search))
                    return this.sortByName(entities)

                let term = this.search;

                return this.sortByName(_.filter(entities, (entity) => entity.name.toUpperCase().includes(term.toUpperCase())))
            }
        },
    }
</script>

<style scoped>

</style>
