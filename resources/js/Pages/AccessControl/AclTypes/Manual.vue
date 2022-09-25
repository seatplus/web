<template>
  <div class="px-4 py-5 sm:p-6">
    <p class="text-base leading-6 text-gray-500">
      This is the very basic member setting. It means you need to manually add users to the role.
    </p>

    <Members
      v-model="members"
      requires-removal-button
      class="mt-6"
    />
    <Users
      v-model="members"
      requires-adding-button
      class="mt-6"
    />
  </div>
</template>

<script>
import Members from "./Members.vue"
import Users from "./Users.vue"

export default {
  name: "Manual",
  components: {Users, Members},
  props: {
    modelValue: {}
  },
  emits: ['update:modelValue'],
  data() {
    return {
      members: this.modelValue.members
    }
  },
  watch: {
    members() {

        let acl = this.modelValue

        acl.affiliations = []
        acl.members = this.members
        acl.moderators = []

      this.$emit('update:modelValue', acl)
    }
  }

}
</script>

<style scoped>

</style>
