<template>
    <Settings :layout-object="this.layoutObject">
        <ul>
            <WideListElement v-for="user in this.users.data" :key="user.id" :url="$route('impersonate.start', user.id)">
                <template v-slot:avatar>
                    <eve-image :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'" :object="user.main_character" :size="128"/>
                </template>
                <template v-slot:upper_left>{{user.main_character.name}}</template>
            </WideListElement>
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

    export default {
        name: "UserList",
        components: {WideListElement, Layout, Settings, Pagination, EveImage},
        props: {
            users: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                search: this.buildSearchParams().get('search_param'),
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
            buildSearchParams: function () {
                return new URL(window.location.href).searchParams;
            },
        },
        watch: {
            search: function () {
                let searchParams = this.buildSearchParams();

                if(searchParams.has('search_param') && this.search === '')
                    searchParams.delete('search_param')

                if(this.search)
                    searchParams.set("search_param", this.search);

                let url = window.location.href.split('?')[0] + '?' + searchParams.toString();

                Inertia.visit(url, {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['users'],
                })
            }
        }
    }
</script>

<style scoped>

</style>
