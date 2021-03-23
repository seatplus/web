<template>
  <div class="overflow-y-auto">
    <ul class="divide-y divide-gray-200 max-h-96">
      <UserDetails :parameters="parameters" />
      <MissingCharacters :parameters="parameters" />
    </ul>
  </div>
</template>

<script>
import UserDetails from "./UserDetails";
import MissingCharacters from "./MissingCharacters";

export default {
    name: "UserCompliance",
    components: {
      MissingCharacters,
      UserDetails
    },
    props: {
        parameters: {
            required: true,
            type: Object
        }
    }

}
</script>

<style scoped>

</style>
