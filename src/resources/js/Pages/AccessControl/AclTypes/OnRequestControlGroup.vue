<template>
    <div class="px-4 py-5 sm:p-6">
        <p class="text-base leading-6 text-gray-500">
            This is the on-request control group settings. Users with characters in the selected affilites can apply to the control group
        </p>

        <Applicants v-model="members" class="mt-6" />

        <SearchCorpOrAlliance v-model="affiliations" class="mt-6">
            Search for corporation or alliance that you wish to affiliate
        </SearchCorpOrAlliance>

        <Affiliations v-model="this.affiliations"></Affiliations>

        <!--Moderators-->
        <Moderators v-model="moderators" class="mt-6" ></Moderators>
        <Users v-model="moderators" class="mt-6" requires-adding-button>
            <template v-slot:title>Available Moderators</template>
            <template v-slot:button-text>Add Moderator</template>
        </Users>


        <Members v-model="members" class="mt-6" />

    </div>
</template>

<script>
  import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance"
  import EveImage from "@/Shared/EveImage"
  import Members from "./Members"
  import Affiliations from "./Affiliations"
  import Applicants from "./Applicants"
  import Users from "./Users"
  import Moderators from "./Moderators"
  export default {
      name: "OnRequestControlGroup",
      components: {Moderators, Users, Applicants, Affiliations, Members, EveImage, SearchCorpOrAlliance},
      props: {
          value: {}
      },
      data() {
          return {
              affiliations: this.value.affiliations,
              members: this.value.members,
              moderators: this.value.moderators
          }
      },
      computed: {
          acl() {
              return {
                  affiliations: this.affiliations,
                  members: this.members,
                  moderators: this.enhancedModerators
              }
          },
          enhancedModerators() {
              return _.map(this.moderators, (moderator) => {
                  moderator.affiliatable_id = moderator.user_id
                  moderator.can_moderate =  true
                  return moderator
              })
          }
      },
      watch: {
          acl(newValue) {
              this.$emit('input', newValue)
          }
      }
  }
</script>

<style scoped>

</style>
