<template>
    <div v-if="applications.length > 0">
        <div class="pb-5 space-y-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Applications
            </h3>
            <p class="max-w-4xl text-sm leading-5 text-gray-500">Below you will find all open applications to review</p>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">

            <UserApplications :applications="applications" />
            <CharacterApplications :applications="applications" />

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
    name: "Applications",
    components: {CharacterApplications, UserApplications, Applicant, EveImage},
    props: {
        corporation_id: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            applications: []
        }
    },
    methods: {
        async getInfo(route) {
            return await axios.get(route)
                .then((response) => {

                    response.data.data.forEach(object => this.applications.push(object))

                    if(response.data.links.next)
                        this.getInfo(response.data.links.next)
                })
                .catch(error => console.log(error))
        }
    },
    created() {
        this.getInfo(this.$route('open.corporation.applications', this.corporation_id));
    }
}
</script>

<style scoped>

</style>
