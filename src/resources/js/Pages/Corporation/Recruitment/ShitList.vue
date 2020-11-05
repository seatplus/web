<template>
    <div v-if="shitters.length > 0">
        <div class="pb-5 space-y-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Shitlist
            </h3>
            <p class="max-w-4xl text-sm leading-5 text-gray-500">Below you will find all every recruit that is no longer would qualify</p>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">

            <UserApplications :applications="shitters" />
            <CharacterApplications :applications="shitters" />

        </div>
    </div>
</template>

<script>
import axios from "axios"
import EveImage from "@/Shared/EveImage";
import Applicant from "./Applicant";
import UserApplications from "./UserApplications";
import CharacterApplications from "./CharacterApplications";

export default {
    name: "ShitList",
    components: {CharacterApplications, UserApplications, Applicant, EveImage},
    props: {
        corporation_id: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            members: []
        }
    },
    methods: {
        async getInfo(route) {
            return await axios.get(route)
                .then((response) => {

                    response.data.data.forEach(object => this.members.push(object))

                    if(response.data.links.next)
                        this.getInfo(response.data.links.next)
                })
                .catch(error => console.log(error))
        },
        hasMissingScopes(character) {

            return _.isEmpty(character) ? null : !_.isEmpty(character.missing_scopes)
        }
    },
    computed: {
        shitters() {

            return _.reject(this.members, (member) => {

                let has_missing_scopes = [member.character?.missing_scopes, ..._.reject(member.characters, (character) => this.hasMissingScopes(character))].filter(Boolean)

                console.log(has_missing_scopes)

                return !_.isEmpty(has_missing_scopes)

                //return this.hasMissingScopes(member?.character) ?? !_.isEmpty(_.reject(member.characters, (character) => this.hasMissingScopes(character)))
            });
        }
    },
    created() {
        this.getInfo(this.$route('corporation.shitlist', this.corporation_id));
    }
}
</script>

<style scoped>

</style>
