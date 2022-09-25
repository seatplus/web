<template>
  <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
    <li
      v-for="role in roles"
      :key="role.id"
      class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow"
    >
      <ControlGroup :role="role" />
    </li>
  </ul>
</template>

<script>

import ControlGroup from "./ControlGroup.vue";
export default {
    name: "ControlGroups",
    components: {ControlGroup},
    props: {
        roles: {
            required: true
        }
    }
}
</script>

<style scoped>

</style>
