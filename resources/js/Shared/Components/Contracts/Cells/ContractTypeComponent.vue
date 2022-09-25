<template>
  <span class="text-sm text-gray-500">
    {{ contract.type }}
  </span>
  <p
    v-if="contract.type !== 'courier'"
    class="flex items-center text-sm text-gray-500"
  >
    <MapPinIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    {{ getStartLocation(contract) }}
  </p>

  <p
    v-if="contract.type === 'courier'"
    class="flex items-center text-sm text-gray-500"
  >
    <MapPinIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Start: {{ getStartLocation(contract) }}
  </p>
  <p
    v-if="contract.type === 'courier'"
    class="flex items-center text-sm text-gray-500"
  >
    <MapPinIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    End: {{ getEndLocation(contract) }}
  </p>
</template>

<script>
import { MapPinIcon } from "@heroicons/vue/20/solid";

export default {
    name: "ContractTypeComponent",
    components: {MapPinIcon},
    props: {
        contract: {
            required: true,
            type: Object
        }
    },
    setup() {

        const getStartLocation = (contract) => _.get(contract, 'start_location.name', _.get(contract, 'start_location.locatable.name', 'unknown'))
        const getEndLocation = (contract) => _.get(contract, 'end_location.name', _.get(contract, 'end_location.locatable.name', 'unknown'))

        return {
            getStartLocation,
            getEndLocation
        }

    },
}
</script>

