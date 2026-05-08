<template>
  <span class="inline-flex rounded-md shadow-sm">
    <button
      type="button"
      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition ease-in-out duration-150"
      @click="clearCache"
    >
      Clear Cache
    </button>
  </span>
</template>

<script>

  import { router } from "@inertiajs/vue3";
  import { clear } from '@/routes/cache'

  export default {
    name: "Commands",

    setup() {
        return { clear }
    },
    methods: {

      clearCache() {
        router.post(clear().url).
        catch(function (error) {
          console.log(error)
        })
      }
    }
  }
</script>

<style scoped>

</style>
