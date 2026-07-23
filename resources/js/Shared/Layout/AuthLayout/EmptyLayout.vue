<template>
  <div>
    <Toasts />
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <slot />
    </div>
  </div>
</template>

<script setup>
import Toasts from "@/Shared/Toasts/Toasts.vue";
</script>

<style scoped>

</style>