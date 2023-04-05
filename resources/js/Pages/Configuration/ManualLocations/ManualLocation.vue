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
  return _.filter(_.map(_.groupBy(suggestions, 'location_id'), (value, prop) => (
      {
        location_id: _.toInteger(prop),
        data: value,
        selected: _.filter(value, 'selected')
      }
  )), location => location.data.length > 1 || _.isEmpty(location.selected))
}
</script>
