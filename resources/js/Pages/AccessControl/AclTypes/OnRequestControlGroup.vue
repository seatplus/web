<template>
  <div class="px-4 py-5 sm:p-6">
    <p class="text-base leading-6 text-gray-500">
      This is the on-request control group settings. Users with characters in the selected affilites can apply to the control group
    </p>

    <div class="grid grid-cols-2 gap-6">
      <div>
        <EsiAutosuggest
          label="Search for Corporation or Alliance"
          :categories="['corporation', 'alliance']"
          placeholder="corporation or alliance name"
          :reset-after-select="true"
          @selected-object="(selectedOption) => affiliations.push(selectedOption)"
        />
        <Affiliations
          v-model="affiliations"
          two-columns
        />
        <Moderators
          v-model="moderators"
          class="mt-6"
          two-columns
        />
        <Users
          v-model="moderators"
          class="mt-6"
          requires-adding-button
          two-columns
        >
          <template #title>
            Available Moderators
          </template>
          <template #button-text>
            Add Moderator
          </template>
        </Users>
      </div>
      <div>
        <Applicants
          v-model="members"
          class="mt-6"
        />
        <Members
          v-model="members"
          class="mt-6"
          requires-removal-button
          two-columns
        />
      </div>
    </div>
    <!--Moderators-->
  </div>
</template>

<script>
import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
import Members from "./Members.vue"
import Affiliations from "./Affiliations.vue"
import Applicants from "./Applicants.vue"
import Users from "./Users.vue"
import Moderators from "./Moderators.vue"
export default {
    name: "OnRequestControlGroup",
    components: {Moderators, Users, Applicants, Affiliations, Members, EsiAutosuggest},
    props: {
        modelValue: {}
    },
    emits: ['update:modelValue', 'updated'],
    data() {
        return {
            affiliations: this.modelValue.affiliations,
            members: this.modelValue.members,
            moderators: this.modelValue.moderators
        }
    },
    computed: {
        acl() {

            let acl = this.modelValue

            acl.affiliations = this.affiliations
            acl.members = this.members
            acl.moderators = this.moderators

            return acl
        },
    },
    watch: {
        acl(newValue) {
            this.$emit('update:modelValue', newValue)
        }
    }
}
</script>

<style scoped>

</style>
