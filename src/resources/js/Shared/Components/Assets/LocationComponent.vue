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
              {{ getLocationsVolume(location.assets) }} volume and {{ getLocationsItemsCount(location.assets) }}
              items
            </p>
          </div>
          <div
            v-if="!location.location && context !== 'recruitment'"
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
    </template>
    <template #elements>
      <ItemList :items="location.assets" />
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
import WideLists from "../../WideLists";
import LocationName from "./LocationName";
import ItemList from "./ItemList";
import AddManualLocationModal from "./AddManualLocationModal";
export default {
    name: "LocationComponent",
    components: {AddManualLocationModal, ItemList, LocationName, WideLists},
    props: {
        location: {
            required: true,
            type: Object
        },
        context: {
            required: false,
            type: String,
            default: 'character'
        }
    },
  data() {
      return {
        openModal: false
      }
  },
    methods: {
        getLocationsVolume(location_assets) {

            function volume(object) {
                return object.type.volume ? object.quantity * object.type.volume : 0;
            }

            const  { prefix } = require('metric-prefix')

            return prefix(_.sum(_.map(location_assets,volume)), { precision: 3, unit: 'm³'})
        },
        getLocationsItemsCount(location_assets) {

            return _.size(location_assets)
        }
    }
}
</script>

<style scoped>

</style>
