<template>
    <Layout :active-sidebar-element="activeSidebarElement">
        <template v-slot:title>
            <PageHeader :breadcrumbs="[{name: 'Control Group', route: 'acl.groups'}]">
                Manage {{ role.name }}
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
                        <select v-model="role_type" id="role_type" aria-label="Selected tab" class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:ring-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                            <option value="opt-in">Opt in</option>
                            <option value="on-request">On Request</option>
                        </select>
                    </div>

                </div>
                <div class="hidden sm:block">
                    <div class="px-4 sm:px-6 border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <button @click="changeRoleType('manual')" :class="[role_type === 'manual' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Manual
                            </button>
                            <button @click="changeRoleType('automatic')" :class="[role_type === 'automatic' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Automatic
                            </button>
                            <button @click="changeRoleType('opt-in')" :class="[role_type === 'opt-in' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Opt In
                            </button>
                            <button @click="changeRoleType('on-request')" :class="[role_type === 'on-request' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                On Request
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-class="transform opacity-100 scale-100"
                xleave-to-class="transform opacity-0 scale-95"
            >
                <div v-if="changed" class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 text-yellow-700">
                                You have unsaved changes. Press save to store the unsaved changes
                            </p>
                        </div>
                    </div>
                </div>
            </transition>

            <Manual v-if="role_type === 'manual'" v-model="acl"/>
            <AutomaticRole v-if="role_type === 'automatic'" v-model="acl" />
            <OptInControlGroup v-if="role_type === 'opt-in'" v-model="acl" />
            <OnRequestControlGroup v-if="role_type === 'on-request'" v-model="acl" />
        </div>



    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import EveImage from "../../Shared/EveImage"
    import PageHeader from "../../Shared/Layout/PageHeader"
    import HeaderButton from "../../Shared/Layout/HeaderButton"
    import ListTransition from "../../Shared/Transitions/ListTransition"
    import AffiliationList from "./AffiliationList"
    import Manual from "./AclTypes/Manual"
    import InputGroup from "../../Shared/InputGroup"
    import AutomaticRole from "./AclTypes/AutomaticRole"
    import OnRequestControlGroup from "./AclTypes/OnRequestControlGroup"
    import OptInControlGroup from "./AclTypes/OptInControlGroup"
    import axios from "axios"

    export default {
        name      : "ManageControlGroup",
        components: {
            OptInControlGroup,
            OnRequestControlGroup,
            AutomaticRole,
            InputGroup,
            Manual, AffiliationList, ListTransition, HeaderButton, PageHeader, Layout, EveImage},
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
                    affiliations: this.role.acl_affiliations,
                    moderators: this.role.moderators
                },
                changed: false
            }
        },
        methods: {
            changeRoleType(type) {

                this.role_type = type
                this.checkIfChanged()
            },
            checkIfChanged() {
                let original = {
                    role_type: this.role.type,
                    acl: {
                        members: this.role.acl_members,
                        affiliations: this.role.acl_affiliations,
                        moderators: this.role.moderators
                    }
                }

                let current = {
                    role_type: this.role_type,
                    acl: this.acl
                }

                this.changed = !_.isEqual(original, current)
            },
            store: function () {

                let data = {
                    type: this.role_type,
                    members: this.acl.members,
                    affiliations: [...this.acl.affiliations, ...this.acl.moderators]
                }

                this.$inertia.post(window.location.href, data, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            }
        },
        watch: {
            acl() {
                this.checkIfChanged()
            }
        },
        computed: {
            activeSidebarElement: function () {
                return route('acl.groups')
            },
        },
    }
</script>

<style scoped>

</style>
