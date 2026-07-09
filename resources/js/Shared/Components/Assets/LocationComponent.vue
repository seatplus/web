<template>
  <WideLists>
    <template #header>
      <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
          <div class="ml-4 mt-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ location.name ?? 'Unknown Location' }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ `${volume} volume and ${numberOfItems} items` }}
            </p>
          </div>
          <div class="inline-flex items-baseline space-x-2">
            <div
              v-if="location.is_manual_location && context !== 'recruitment'"
              class="ml-4 mt-4 shrink-0"
            >
              <button
                type="button"
                class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-xs text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="openModal = true"
              >
                Add location information
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #elements>
        <ItemList
            :key="location.assets.length"
            :items="location.assets"
            :compact="compact"
        />
    </template>
  </WideLists>
  <teleport to="#destination">
    <AddManualLocationModal
      v-model="openModal"
      :location_id="location.location_id"
    />
  </teleport>
</template>

<script setup>
import WideLists from "../../WideLists.vue";
import ItemList from "./ItemList.vue";
import AddManualLocationModal from "./AddManualLocationModal.vue";
import {computed, ref} from "vue";
import {prefix} from "metric-prefix";

const props = defineProps({
    location: {
        required: true,
        type: Object
    },
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
})

const openModal = ref(false)

const volume = computed(() => {
    return prefix(props.location.volume, { precision: 3, unit: 'm³'})
})

const numberOfItems = computed(() => _.size(props.location.assets))

</script>
