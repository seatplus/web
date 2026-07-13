<template>
  <div>
    <!-- Native Inertia v3 infinite scroll over the page-level `assets` scroll prop
         (locations paginated with pageName 'assets'). Replaces the axios/Ziggy
         InfiniteLoadingHelper + useInfinityScrolling loader. -->
    <InfiniteScroll
      data="assets"
      items-element="#assets-body"
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
