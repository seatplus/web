<template>
    <Settings :layout-object="this.layoutObject">
        <ul>
            <li class="px-4 py-4 sm:px-6">

                <label for="search_field" class="sr-only">Search</label>
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input v-model="search" id="search_field" class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm" placeholder="Search" type="search" name="search">
                </div>


            </li>
            <UserListElement v-for="user in this.users.data" :key="user.id" :user="user"/>
        </ul>


        <template slot="footer">
            <pagination :collection="users"/>
        </template>

    </Settings>

</template>

<script>
    import EveImage from "../../Shared/EveImage"
    import Pagination from "../../Shared/Pagination"
    import {Inertia} from "@inertiajs/inertia"
    import Settings from "./Settings"
    import Layout from "../../Shared/Layout"
    import WideListElement from "../../Shared/WideListElement"
    import EntityBlock from "../../Shared/Layout/Eve/EntityBlock";
    import UserListElement from "./UserListElement";

    export default {
        name: "UserList",
        components: {UserListElement, EntityBlock, WideListElement, Layout, Settings, Pagination, EveImage},
        props: {
            users: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                search: this.getSearchParams(),
                layoutObject: {
                    pageHeader: 'Server Settings',
                    pageDescription: 'User List'
                }
            }
        },
        methods: {
            characterWithoutMain(user) {

                return _.reject(user.characters, function (character) {
                    const {main_character} = user
                    return _.isEqual(character.character_id, main_character.character_id)
                })
            },
            getSearchParams() {
                let params = this.$route().params

                return _.get(params, 'search_param', '')
            }
        },
        watch: {
            search() {

                let params = this.$route().params

                if(_.has(params,'search_param') && this.search === '')
                    _.unset(params, 'search_param')

                if(this.search)
                    _.set(params,"search_param", this.search);

                _.set(params, 'page', 1)

                let url_name = this.$route().current()

                Inertia.visit(this.$route(url_name, params), {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['users'],
                })
            }
        },
        computed: {
            searchparam() {
                return this.$route().params
            }
        }
    }
</script>

<style scoped>

</style>
