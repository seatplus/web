<template>
  <div>
    <div class="space-y-2 sm:space-y-6">
      <LocationComponent
        v-for="location in result"
        :key="location.location_id"
        :location="location"
        :context="context"
        :compact="compact"
        :query-parameters="parameters"
      />
    </div>
    <div ref="scrollComponent"></div>
  </div>
</template>

<script>
import LocationComponent from "./LocationComponent";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
export default {
    name: "AssetsComponent",
    components: { LocationComponent,
        //InfiniteLoading
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

        return useInfinityScrolling('load.character.assets', props.parameters)
    },
    data() {
        return {
            openModal: false,
            modal_location_id: 0
        }
    },
}
</script>

<style scoped>

</style>
