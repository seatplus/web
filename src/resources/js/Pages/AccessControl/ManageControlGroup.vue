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
                            <button @click="role_type = 'manual'" :class="[role_type === 'manual' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Manual
                            </button>
                            <button @click="role_type = 'automatic'" :class="[role_type === 'automatic' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Automatic
                            </button>
                            <button @click="role_type = 'opt-in'" :class="[role_type === 'opt-in' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
                                Opt In
                            </button>
                            <button @click="role_type = 'on-request'" :class="[role_type === 'on-request' ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700': 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','ml-8 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm leading-5  focus:outline-none']">
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
                class="z-10"
            >
                <div v-if="changed" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 hidden sm:block">
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

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-class="transform opacity-100 scale-100"
                xleave-to-class="transform opacity-0 scale-95"
            >

                <!-- This example requires Tailwind CSS v2.0+ -->
                <div v-if="changed" class="fixed bottom-0 inset-x-0 pb-2 sm:hidden">
                    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                        <div class="p-2 rounded-lg bg-indigo-600 shadow-lg sm:p-3">
                            <div class="flex items-center justify-between flex-wrap">
                                <div class="w-0 flex-1 flex items-center">
                                      <span class="flex p-2 rounded-lg bg-indigo-800">
                                        <!-- Heroicon name: outline/speakerphone -->
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                      </span>
                                    <p class="ml-3 font-medium text-white truncate">
                                        <span class="md:hidden">
                                          You have unsaved changes.
                                        </span>
                                    </p>
                                </div>
                                <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                                    <inertia-link method="post" as="div" :href="store_url" :data="store_payload" class="flex cursor-pointer items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                                        Save
                                    </inertia-link>
                                </div>
                                <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
                                    <button @click="changed = false" type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Dismiss</span>
                                        <!-- Heroicon name: outline/x -->
                                        <svg class="h-6 w-6 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </transition>
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
                store_url: this.$route('acl.manage', this.role.id)
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

                this.$inertia.post(window.location.href, this.store_payload, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            }
        },
        computed: {
            activeSidebarElement: function () {
                return route('acl.groups')
            },
            changed() {
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

                return !_.isEqual(original, current)
            },
            store_payload() {
                return {
                    type: this.role_type,
                    members: this.acl.members,
                    affiliations: [...this.acl.affiliations, ...this.acl.moderators]
                }
            },
        },
    }
</script>

<style scoped>

</style>
