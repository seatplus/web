<template>
    <div class="px-4 py-5 sm:p-6">
        <p class="text-base leading-6 text-gray-500">
            This is the automatic control group settings. Users with characters in the selected affilites will gain access to the control group
        </p>

        <SearchCorpOrAlliance v-model="selectedEntities" class="mt-6">
            Search for corporation or alliance that you wish to affiliate
        </SearchCorpOrAlliance>

        <Affiliations v-model="this.selectedEntities"></Affiliations>

        <Members v-model="members" class="mt-6" />

    </div>
</template>

<script>
  import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance"
  import EveImage from "@/Shared/EveImage"
  import Members from "./Members"
  import Affiliations from "./Affiliations"
  export default {
      name: "AutomaticRole",
      components: {Affiliations, Members, EveImage, SearchCorpOrAlliance},
      props: {
          value: {}
      },
      data() {
          return {
              selectedEntities: this.value.affiliations,
              members: this.value.members
          }
      },
      computed: {
          acl() {
              return {

              }
          }
      },
      watch: {

      }
  }
</script>

<style scoped>

</style>
