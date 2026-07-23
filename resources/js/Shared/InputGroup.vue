<template>
  <div>
    <label
      :for="label"
      class="block text-sm font-medium leading-5 text-gray-700 capitalize"
    >{{ label }}</label>
    <slot class="border-red-300" />
  </div>
</template>

<script setup>
defineProps({
    for: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    }
});
</script>

<style scoped>

</style>
