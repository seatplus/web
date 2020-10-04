<template>
    <ul class="divide-y divide-gray-200">
        <Applicant v-for="applicant in character_applications" :key="applicant.character_id" :character="applicant" />
    </ul>
</template>

<script>
import Applicant from "./Applicant";
export default {
    name: "CharacterApplications",
    components: {Applicant},
    props: {
        applications: {
            required: true,
            type: Array
        }
    },
    computed: {
        character_applications() {
            return _.filter(this.applications, (application) => !application.is_user)
        }
    },
}
</script>

<style scoped>

</style>
