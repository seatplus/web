<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <Deferred data="data">
      <template #fallback>
        <div class="animate-pulse space-y-3">
          <div class="h-24 rounded-lg bg-gray-100" />
          <div class="h-24 rounded-lg bg-gray-100" />
        </div>
      </template>

      <ManualLocationComponent
        v-for="(location, index) in groupSuggestions(data)"
        :key="location.location_id"
        :index="index"
        :location="location"
        @on-submitted-suggestion="refresh"
      />
    </Deferred>
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ManualLocationComponent from "./ManualLocationComponent.vue";
import { Deferred, router } from "@inertiajs/vue3";

const pageTitle = 'Manual Locations'

defineProps({
  // Deferred prop: the full ManualLocation suggestion list, resolved server-side.
  data: {
    type: Array,
    default: () => [],
  },
})

// After a suggestion is accepted, re-resolve just the deferred list.
const refresh = () => router.reload({ only: ['data'] })

// Group the flat suggestion list by location_id (competing suggestions per location),
// unselected groups first.
const groupSuggestions = (suggestions) => {
  const groupedSuggestions = _.groupBy(suggestions, 'location_id')

  const mappedSuggestions = _.map(groupedSuggestions, (value, prop) => ({
    location_id: _.toInteger(prop),
    data: value,
    selected: _.filter(value, 'selected'),
  }))

  return _.sortBy(mappedSuggestions, 'selected')
}
</script>
