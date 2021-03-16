<template>
  <div v-if="hasResults">
    <div class="pb-5 space-y-2">
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Applications
      </h3>
      <p class="max-w-4xl text-sm leading-5 text-gray-500">
        Below you will find all open applications to review
      </p>
    </div>
    <div class="bg-white sm:rounded-md">
      <UserApplications :applications="result" />
      <CharacterApplications :applications="result" />
    </div>
  </div>
  <div ref="scrollComponent"></div>
</template>

<script>
import UserApplications from "./UserApplications";
import CharacterApplications from "./CharacterApplications";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
    name: "Applications",
    components: {CharacterApplications, UserApplications},
    props: {
        parameters: {
            type: Object,
            required: true
        },
    },
  setup(props) {
    return useInfinityScrolling('open.corporation.applications', props.parameters)
  },
  computed: {
    hasResults() {
      return this.result.length > 0;
    }
  }
}
</script>

<style scoped>

</style>
