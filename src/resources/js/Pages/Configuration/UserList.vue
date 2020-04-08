<template>
    <Settings :layout-object="this.layoutObject">
        <!--<b-container>
            <b-row>
                <b-col md="6"/>
                <b-col md="6">
                    <b-form-group
                        label="Search for a user"
                        description="type any name you look for"
                        label-cols-sm="3"
                    >
                        <b-form-input type="text" v-model="search"/>
                    </b-form-group>
                </b-col>
            </b-row>
        </b-container>
        <table class="table table-responsive-md table-sm">

            <thead class="thead-light">
            <tr>
                <th>Main character</th>
                <th>Other characters</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody v-for="user in users.data">
            <tr>
                <td>
                    <div class="d-flex flex-row" v-if="user.main_character">
                        <div class="p-2"><EveImage :object="user.main_character" :size="32" /></div>
                        <div class="p-2 align-self-center">{{user.main_character.name}}</div>
                    </div>
                    <div v-else>
                        Data has not been fetched for user with id {{user.id}}
                    </div>
                <td>
                    <div class="d-flex flex-row" v-for="character in characterWithoutMain(user)">
                        <div class="p-2"><EveImage :object="character" :size="32" /></div>
                        <div class="p-2 align-self-center">{{character.name}}</div>
                    </div>
                </td>
                <td>
                    <inertia-link v-if="user.id !== $page.user.data.id"
                                  :href="route('impersonate.start', user.id)"
                                  class="btn btn-primary p-2"
                    > impersonate
                    </inertia-link>
                </td>
            </tr>
            </tbody>
        </table>-->
        <ul>
            <WideListElement v-for="user in this.users.data" :key="user.id" url="#">
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
