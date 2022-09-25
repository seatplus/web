<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <InfiniteLoadingHelper
      :key="infiniteId"
      v-slot="{results}"
      route-name="get.manuel_locations.suggestions"
    >
      <ManualLocationComponent
        v-for="(location, index) in groupSuggestions(results)"
        :key="`${location.location_id}:${infiniteId}`"
        :index="index"
        :location="location"
        @onSubmittedSuggestion="reset"
      />
    </InfiniteLoadingHelper>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ManualLocationComponent from "./ManualLocationComponent.vue";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";


export default {
    name: "ManualLocation",
    components: {
        InfiniteLoadingHelper, ManualLocationComponent, PageHeader,
        //InfiniteLoading
    },
    setup() {
        //return useInfinityScrolling('get.manuel_locations.suggestions')
    },
    data() {
        return {
            infiniteId: +new Date(),
            pageTitle: 'Manual Locations'
        }
    },
    methods: {
        reset() {
            this.infiniteId++
            console.log('resetting')
        },
        groupSuggestions(suggestions) {
            return _.filter(_.map(_.groupBy(suggestions, 'location_id'), (value, prop) => (
                {
                    location_id: _.toInteger(prop),
                    data: value,
                    selected: _.filter(value, 'selected')
                }
            )), location => location.data.length > 1 || _.isEmpty(location.selected))
        }
    }
}
</script>

<style scoped>

</style>
