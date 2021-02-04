<template>
    <div class="overflow-y-auto">
        <ul class="divide-y divide-gray-200 max-h-96" >
            <li v-for="user of users">
                <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                    <div class="flex items-center px-4 py-4 sm:px-6">
                        <div class="min-w-0 flex-1 flex items-center">
                            <div class="flex-shrink-0 inline-flex items-center space-x-2">
                                <EveImage tailwind_class="h-12 w-12 rounded-full" :size="256" :object="user.main_character" />
                                <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                    {{ user.main_character.name }}
                                </div>
                                <!--<img class="h-12 w-12 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">-->
                            </div>
                            <div class="min-w-0 flex-1 px-4"> <!--md:grid md:grid-cols-2 md:gap-4-->
                                <ul class="divide-y divide-gray-200">
                                    <CharacterComplianceElement v-for="character of user.characters" :key="character.character.character_id" :character="character" />
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <infinite-loading :identifier="infiniteId" @infinite="loadUsers" spinner="waveDots" >
                <div class="hidden" slot="no-more" />
            </infinite-loading>
            <CharacterComplianceElement v-for="character of characters" :key="character.character.character_id" :character="character" />
            <infinite-loading :identifier="infiniteId" @infinite="loadCharacters" spinner="waveDots" >
                <div slot="no-more">all loaded</div>
            </infinite-loading>

        </ul>
    </div>
</template>

<script>
import axios from "axios";
import EveImage from "@/Shared/EveImage";
import CharacterComplianceElement from "./CharacterComplianceElement";
import InfiniteLoading from "vue-infinite-loading";

export default {
    name: "UserCompliance",
    components: {CharacterComplianceElement, EveImage, InfiniteLoading},
    props: {
        corporation_id: {
            required: true,
            type: Number
        },
        queryParam: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            user_page: 1,
            character_page: 1,
            users: [],
            characters: []
        }
    },
    methods: {
        loadUsers($state) {
            const self = this;

            axios.get(this.getRoute, {
                params: {
                    page: this.user_page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.user_page += 1;

                    let user_data = _.filter([...response.data.data], (data) => !_.isEmpty(data))

                    this.users.push(...user_data)

                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
        loadCharacters($state) {
            const self = this;

            axios.get(this.getCharacterRoute, {
                params: {
                    page: this.character_page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.character_page += 1;

                    let character_data = _.filter([...response.data.data], (data) => !_.isEmpty(data))

                    this.characters.push(...character_data)

                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        }
    },
    computed: {
        getRoute() {

            let parameter = this.queryParam === '' ? null : this.queryParam;

            return this.$route('user.compliance', {corporation_id: this.corporation_id, filter: parameter})
        },
        getCharacterRoute() {

            let parameter = this.queryParam === '' ? null : this.queryParam;

            return this.$route('missing.characters.compliance', {corporation_id: this.corporation_id, filter: parameter})
        },
    },
    watch: {
        queryParam() {
            this.users = [];
            this.characters = [];
            this.user_page = 1;
            this.character_page = 1;
            this.infiniteId += 1
        },
    }
}
</script>

<style scoped>

</style>
