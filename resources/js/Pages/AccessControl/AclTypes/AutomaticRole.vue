<template>
  <div class="px-4 py-5 sm:p-6 space-y-4">
    <p class="text-base leading-6 text-gray-500">
      This is the automatic control group settings. Users with characters in the selected affilites will gain access to the control group
    </p>

    <SearchCorpOrAlliance
      v-model="affiliations"
      class="mt-6"
    >
      Search for corporation or alliance that you wish to affiliate
    </SearchCorpOrAlliance>

    <Affiliations v-model="affiliations" />

    <Members
      v-model="members"
      class="mt-6"
    />
  </div>
</template>

<script>
  import SearchCorpOrAlliance from "@/Shared/SearchCorpOrAlliance.vue"
  import Members from "./Members.vue"
  import Affiliations from "./Affiliations.vue"
  export default {
      name: "AutomaticRole",
      components: {Affiliations, Members, SearchCorpOrAlliance},
      props: {
          modelValue: {}
      },
emits: ['update:modelValue'],
      data() {
          return {
              affiliations: this.modelValue.affiliations,
              members: this.modelValue.members
          }
      },
      computed: {
          acl() {

              let acl = this.modelValue

              acl.affiliations = this.affiliations
              acl.members = this.members
              acl.moderators = []

              return acl
          }
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
