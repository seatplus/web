<template>
  <div class="px-4 py-5 sm:p-6">
    <p class="text-base leading-6 text-gray-500">
      This is the opt in control group settings. Users with characters in the selected affilites can freely join or leave
    </p>

    <EsiAutosuggest
      label="Search for Corporation or Alliance"
      :categories="['corporation', 'alliance']"
      placeholder="corporation or alliance name"
      :reset-after-select="true"
      @selected-object="(selectedOption) => affiliations.push(selectedOption)"
    />

    <Affiliations v-model="affiliations" />

    <Members
      v-model="members"
      class="mt-6"
      requires-removal-button
    />
  </div>
</template>

<script>
  import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
  import Members from "./Members.vue"
  import Affiliations from "./Affiliations.vue"
  export default {
      name: "OptInControlGroup",
      components: {Affiliations, Members, EsiAutosuggest},
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
