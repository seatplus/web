<template>
    <div class="px-4 py-5 sm:p-6">
        <p class="text-base leading-6 text-gray-500">
            This is the very basic member setting. It means you need to manually add users to the role.
        </p>

        <Members v-model="members" requires-removal-button class="mt-6"/>
        <Users v-model="members" requiresAddingButton class="mt-6" />


    </div>
</template>

<script>
  import EveImage from "@/Shared/EveImage"
  import Members from "./Members"
  import Users from "./Users"

  export default {
      name: "Manual",
      components: {Users, Members, EveImage},
      props: {
          value: {}
      },
      data() {
          return {
              members: this.value.members
          }
      },
      watch: {
          members() {
              this.$emit('input', {
                  members: this.members,
                  affiliations: [],
                  moderators: []
              })
          }
      }

  }
</script>

<style scoped>

</style>
