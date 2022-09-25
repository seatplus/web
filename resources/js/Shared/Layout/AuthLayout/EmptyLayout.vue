<template>
  <div>
    <Notifications />
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <slot />
    </div>
  </div>
</template>

<script>
import Notifications from "../../Notifications/Notifications.vue";

export default {
    name: "EmptyLayout",
    components: {Notifications}
}
</script>

<style scoped>

</style>