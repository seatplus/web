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
      route="get.manuel_locations.suggestions"
      @result="(result) => results = result"
    >
      <ManualLocationComponent
        v-for="(location, index) in grouped_suggestions"
        :key="`${location.location_id}:${infiniteId}`"
        :index="index"
        :location="location"
        @onSubmittedSuggestion="reset"
      />
    </InfiniteLoadingHelper>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import ManualLocationComponent from "./ManualLocationComponent";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";


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
            pageTitle: 'Manual Locations',
            results: []
        }
    },
    computed: {
        grouped_suggestions() {
            return _.filter(_.map(_.groupBy(this.results, 'location_id'), (value, prop) => (
                {
                    location_id: _.toInteger(prop),
                    data: value,
                    selected: _.filter(value, 'selected')
                }
            )), location => location.data.length > 1 || _.isEmpty(location.selected))
        }
    },
    methods: {
        reset() {
            this.infiniteId++
            console.log('resetting')
        }
    }
}
</script>

<style scoped>

</style>
