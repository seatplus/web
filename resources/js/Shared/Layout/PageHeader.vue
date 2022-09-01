<template>
  <div>
    <div v-if="breadcrumbs">
      <nav class="sm:hidden">
        <Link
          :href="getBack.route"
          class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out"
        >
          <svg
            class="shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
          {{ getBack.name }}
        </Link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <template v-for="breadcrumb of breadcrumbs">
          <Link
            :href="breadcrumb.route"
            class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out"
          >
            {{ breadcrumb.name }}
          </Link>
          <svg
            v-if="breadcrumb !== getBack"
            class="shrink-0 mx-2 h-5 w-5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          <!--Page Header-->
          <slot />
        </h2>
      </div>
      <div class="mt-4 shrink-0 flex md:mt-0 md:ml-4 space-x-3">
        <slot name="secondary">
          <span class="shadow-sm rounded-md" />
        </slot>
        <slot name="primary" />
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
  export default {
      name: "PageHeader",
      components: {Link},
      props: ['breadcrumbs'],
      computed: {
          getBack() {
              return _.last(this.breadcrumbs)
          }
      }
  }
</script>

<style scoped>

</style>
