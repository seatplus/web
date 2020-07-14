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
                    <label for="role_type" class="block text-sm font-medium leading-5 text-gray-700">
                        Select role type
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <select v-model="role_type" id="role_type" aria-label="Selected tab" class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                            <option value="on-request">On Request</option>
                            <option value="hidden">Secret</option>
                        </select>
                    </div>

                </div>
                <div class="hidden sm:block">
                    <div class="px-4 sm:px-6 border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <button @click="changeRoleType('manual')" :class="[role_type === 'manual' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','whitespace-no-wrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Manual
                            </button>
                            <button @click="changeRoleType('automatic')" :class="[role_type === 'automatic' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-no-wrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Automatic
                            </button>
                            <button @click="changeRoleType('on-request')" :class="[role_type === 'on-request' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-no-wrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                On Request
                            </button>
                            <button @click="changeRoleType('hidden')" :class="[role_type === 'hidden' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-no-wrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Secret
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <Manual v-if="role_type === 'manual'" v-model="acl"/>
            <AutomaticRole v-if="role_type === 'automatic'" v-model="acl"></AutomaticRole>
            <OnRequestControlGroup v-if="role_type === 'on-request'" v-model="acl" />
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
    import Manual from "./AclTypes/Manual"
    import InputGroup from "../../Shared/InputGroup"
    import AutomaticRole from "./AclTypes/AutomaticRole"
    import OnRequestControlGroup from "./AclTypes/OnRequestControlGroup"

    export default {
        name      : "ManageControlGroup",
        components: {
            OnRequestControlGroup,
            AutomaticRole,
            InputGroup,
            Manual, AffiliationList, ListTransition, HeaderButton, PageHeader, Layout, Multiselect, EveImage},
        props: {
            role: {
                required: true
            },
        },
        data() {
            return {
                role_type: this.role.type,
                acl: {
                    members: this.role.acl_members,
                    affiliations: this.role.acl_affiliations
                }
            }
        },
        methods: {
            changeRoleType(type) {
               this.role_type = type
            },
            store: function () {

                let data = {
                    type: this.role_type,
                    members: this.acl.members,
                    affiliations: this.acl.affiliations
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
        },
    }
</script>

<style scoped>

</style>
