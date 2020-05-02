<template>
    <div>
        <label :for="this.label" class="block text-sm font-medium leading-5 text-gray-700 capitalize">{{ this.label }}</label>
        <slot class="border-red-300"></slot>
    </div>
</template>

<script>
  export default {
      name: "InputGroup",
      props: {
          for: {
              type: String,
              required: true
          },
          label: {
              type: String,
              required: true
          }
      },
      data() {
          return {
              error: ''
          }
      },
      mounted() {

      }
  }
</script>

<style scoped>

</style>
