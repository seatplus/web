<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <CompleteInertiaLoaderHelper
      :key="infiniteId"
      v-slot="{ data }"
    >
      <ManualLocationComponent
        v-for="(location, index) in groupSuggestions(data)"
        :key="location.location_id"
        :index="index"
        :location="location"
        @on-submitted-suggestion="reset"
      />
    </CompleteInertiaLoaderHelper>
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ManualLocationComponent from "./ManualLocationComponent.vue";
import CompleteInertiaLoaderHelper from "@/Shared/CompleteInertiaLoaderHelper.vue";
import {ref} from "vue";

const pageTitle = 'Manual Locations'
const infiniteId = ref(+new Date())

// create function reset that will be called when the event on-submitted-suggestion is emitted
const reset = () => infiniteId.value++

// create function groupSuggestions that will be called when the data is loaded
const groupSuggestions = (suggestions) => {

  // group the suggestions by location_id
  let groupedSuggestions = _.groupBy(suggestions, 'location_id')

  // map the grouped suggestions
  let mappedSuggestions = _.map(groupedSuggestions, (value, prop) => (
      {
        location_id: _.toInteger(prop),
        data: value,
        selected: _.filter(value, 'selected')
      }
  ))

  // sort the mapped suggestions by selected where unselcted are first
  // return the sorted suggestions
  return _.sortBy(mappedSuggestions, 'selected')

}
</script>
