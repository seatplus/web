<template>
  <div class="relative">
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
        class="space-y-2 sm:space-y-6 transition-opacity duration-150"
        :class="{ 'opacity-40': loading }"
      >
        <LocationComponent
          v-for="location in locations"
          :key="location.location_id"
          :location="location"
          :context="context"
          :compact="compact"
          :filter="filter"
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

    <!-- Filter/search in flight: overlay a small "updating" pill above the dimmed list. -->
    <div
      v-if="loading"
      class="pointer-events-none absolute inset-x-0 top-0 flex justify-center pt-6"
    >
      <div class="inline-flex items-center gap-x-2 rounded-full bg-white/90 px-4 py-2 shadow-sm ring-1 ring-black/5">
        <svg
          class="animate-spin h-5 w-5 text-gray-400"
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
        <span class="text-sm font-medium text-gray-500">updating…</span>
      </div>
    </div>
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
        },
        loading: {
            required: false,
            default: false,
            type: Boolean
        },
        // The applied asset filter, forwarded to each location's lazy per-location fetch.
        filter: {
            required: false,
            type: Object,
            default: () => ({})
        }
    },
    computed: {
        locations() {
            return this.$page.props.assets?.data ?? []
        }
    }
}
</script>
