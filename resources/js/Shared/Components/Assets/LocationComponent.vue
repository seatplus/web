<template>
  <WideLists>
    <template #header>
      <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
          <div class="ml-4 mt-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              <LocationName :location="location" />
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ `${volume} volume and ${numberOfItems} items` }}
            </p>
          </div>
          <div class="inline-flex items-baseline space-x-2">
            <div
              v-if="!location.location && context !== 'recruitment' && !(location.location_id === 2004)"
              class="ml-4 mt-4 flex-shrink-0"
            >
              <button
                type="button"
                class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
      <CompleteLoadingHelper
        :key="Object.values(enrichedQueryParameters).join(',')"
        route-name="location.assets"
        :params="enrichedQueryParameters"
        @results="(results) => rawResults = results"
      >
        <ItemList
          :key="items.length"
          :items="items"
          :compact="compact"
        />
      </CompleteLoadingHelper>
    </template>
  </WideLists>
  <teleport to="#destination">
    <AddManualLocationModal
      v-model="openModal"
      :location_id="location.location_id"
    />
  </teleport>
</template>

<script>
import WideLists from "../../WideLists.vue";
import LocationName from "./LocationName.vue";
import ItemList from "./ItemList.vue";
import AddManualLocationModal from "./AddManualLocationModal.vue";
import CompleteLoadingHelper from "../../Layout/CompleteLoadingHelper.vue";
import {computed, ref} from "vue";
import {prefix} from "metric-prefix";

export default {
    name: "LocationComponent",
    components: {CompleteLoadingHelper, AddManualLocationModal, ItemList, LocationName, WideLists},
    props: {
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
        queryParameters: {
            required: true,
            type: Object
        }
    },
    setup(props) {

        const enrichedQueryParameters = _.merge({ location_id: props.location.location_id }, props.queryParameters)
        const rawResults = ref([])

        const volume = computed(() => {
            let sum = _.sumBy(rawResults.value, (object) => _.get(object , 'type.volume', 0) * _.get(object, 'quantity', 0))

            return prefix(sum, { precision: 3, unit: 'm³'})
        })

        const numberOfItems = computed(() => _.size(rawResults.value))

        const items = computed(() => rawResults.value)


        return {
            enrichedQueryParameters,
            rawResults,
            volume,
            items,
            numberOfItems
        }
    },
    data() {
        return {
            openModal: false
        }
    },
}
</script>