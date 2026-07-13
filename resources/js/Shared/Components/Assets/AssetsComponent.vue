<template>
  <div>
    <!-- Native Inertia v3 infinite scroll over the page-level `assets` scroll prop
         (locations paginated with pageName 'assets'). Replaces the axios/Ziggy
         InfiniteLoadingHelper + useInfinityScrolling loader. -->
    <InfiniteScroll
      data="assets"
      items-element="#assets-body"
      :buffer="750"
      preserve-url
    >
      <div
        id="assets-body"
        class="space-y-2 sm:space-y-6"
      >
        <LocationComponent
          v-for="location in locations"
          :key="location.location_id"
          :location="location"
          :context="context"
          :compact="compact"
        />
      </div>

      <!-- Shown in whichever trigger is fetching the next/previous page. -->
      <template #loading>
        <div class="relative block w-full py-6 text-center">
          <svg
            class="animate-spin mx-auto h-8 w-8 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          <span class="mt-2 block text-sm font-medium text-gray-500">
            loading more locations…
          </span>
        </div>
      </template>
    </InfiniteScroll>
  </div>
</template>

<script>
import LocationComponent from "./LocationComponent.vue";
import { InfiniteScroll } from "@inertiajs/vue3";

export default {
    name: "AssetsComponent",
    components: {
        InfiniteScroll,
        LocationComponent,
    },
    props: {
        context: {
            required: false,
            type: String,
            default: 'character'
        },
        compact: {
            required: false,
            default: false,
            type: Boolean
        }
    },
    computed: {
        locations() {
            return this.$page.props.assets?.data ?? []
        }
    }
}
</script>
