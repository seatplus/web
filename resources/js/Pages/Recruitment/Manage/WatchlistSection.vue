<template>
  <div class="border-t border-gray-100 pt-3 space-y-3">
    <button
      type="button"
      class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
      @click="open = !open"
    >
      {{ open ? 'Hide' : 'Manage' }} watchlist
    </button>

    <div
      v-if="open"
      class="space-y-4"
    >
      <p class="text-xs text-gray-500">
        Items, assets and contracts matching this watchlist are highlighted while reviewing an
        applicant (and, later, while observing an employee).
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <EsiMultiselect
          v-model="geoForm.regions"
          :categories="['region']"
          label="Regions"
          placeholder="Search for a region"
        />
        <EsiMultiselect
          v-model="geoForm.systems"
          :categories="['solar_system']"
          label="Solar systems"
          placeholder="Search for a solar system"
        />
      </div>
      <div class="flex justify-end">
        <button
          type="button"
          :disabled="geoForm.processing"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
          @click="saveLocations"
        >
          Save locations
        </button>
      </div>

      <ItemsWatchlist
        :items="watched.items"
        :corporation-id="corporationId"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import ItemsWatchlist from "@/Pages/Corporation/Recruitment/Configuration/ItemsWatchlist.vue";

const props = defineProps({
  corporationId: {
    required: true,
    type: Number,
  },
  watched: {
    required: true,
    type: Object,
  },
  watchlistUrl: {
    required: true,
    type: String,
  },
});

const open = ref(false);

// Locations (regions/systems) save independently of the item watchlist; the backend action applies a
// partial update, so posting only these keys leaves the item selection untouched.
const geoForm = useForm({
  systems: props.watched.systems,
  regions: props.watched.regions,
});

function saveLocations() {
  geoForm.post(props.watchlistUrl, { preserveScroll: true });
}
</script>
