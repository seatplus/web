<template>
  <div class="bg-white shadow overflow-hidden rounded-md">
    <ul class="divide-y divide-gray-200">
      <Applicant
        v-for="applicant in character_applications"
        :key="applicant.character_id"
        :character="applicant"
        :href="$route('character.application', applicant.character.character_id)"
      />
    </ul>
  </div>
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
            return _.map(_.filter(this.applications, (application) => !application.is_user), (user) => user.character)
        }
    },
}
</script>

<style scoped>

</style>
