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

        <Members v-model="members" class="mt-6" />

    </div>
</template>

<script>
  import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance"
  import EveImage from "@/Shared/EveImage"
  import Members from "./Members"
  import Affiliations from "./Affiliations"
  import Applicants from "./Applicants"
  export default {
      name: "OnRequestControlGroup",
      components: {Applicants, Affiliations, Members, EveImage, SearchCorpOrAlliance},
      props: {
          value: {}
      },
      data() {
          return {
              affiliations: this.value.affiliations,
              members: this.value.members
          }
      },
      computed: {
          acl() {
              return {
                  affiliations: this.affiliations,
                  members: this.members
              }
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
