<template>
  <span class="ml-3 shadow-sm rounded-md">
    <button
      type="button"
      :class="[secondary ? 'border-gray-300 text-gray-700 bg-white hover:text-gray-500 focus:ring-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50' : 'border-transparent text-white bg-indigo-600 hover:bg-indigo-500 focus:ring-indigo focus:border-indigo-700 active:bg-indigo-700','inline-flex items-center px-4 py-2 border  text-sm leading-5 font-medium rounded-md focus:outline-none transition duration-150 ease-in-out']"
    >
      <slot />
    </button>
  </span>
</template>

<script>
  export default {
      name: "HeaderButton",
      props: {
          secondary: {
              default: false
          }
      },
  }
</script>

<style scoped>

</style>
