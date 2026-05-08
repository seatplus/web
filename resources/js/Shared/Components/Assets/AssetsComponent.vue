<template>
  <div>
    <InfiniteLoadingHelper
      :key="loadingHelperKey"
      v-slot="{results}"
      :url="locations({ query: parameters }).url"
    >
      <div class="space-y-2 sm:space-y-6">
        <LocationComponent
          v-for="location in results"
          :key="location.location_id"
          :location="location"
          :context="context"
          :compact="compact"
        />
      </div>
    </InfiniteLoadingHelper>
  </div>
</template>

<script>
import LocationComponent from "./LocationComponent.vue";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper.vue";
import { ref, watch } from "vue";
import { locations } from '@/routes/get/character/assets'
export default {
    name: "AssetsComponent",
    components: {
        InfiniteLoadingHelper, LocationComponent,
    },
    props: {
        parameters: {
            type: Object,
            required: true
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
        }
    },
    setup(props) {

        const loadingHelperKey = ref(+new Date())

        const debounce = _.debounce(() => loadingHelperKey.value++ , 350)

        watch(() => props.parameters, () => debounce())

        return {
            locations,
            loadingHelperKey
        }
    },
    data() {
        return {
            openModal: false,
            modal_location_id: 0
        }
    },
}
</script>
