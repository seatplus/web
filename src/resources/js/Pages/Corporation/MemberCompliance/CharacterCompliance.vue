<template>
  <div class="overflow-y-auto">
    <ul class="divide-y divide-gray-200 max-h-96">
      <CharacterComplianceElement
        v-for="character of result"
        :key="character.character.character_id"
        :character="character"
      />
      <div ref="scrollComponent"></div>
    </ul>
  </div>
</template>

<script>
import CharacterComplianceElement from "./CharacterComplianceElement";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
    name: "CharacterCompliance",
    components: {
      CharacterComplianceElement
    },
    props: {
        parameters: {
            required: true,
            type: Object
        }
    },
  setup(props) {

    return useInfinityScrolling('character.compliance', props.parameters)

  }
}
</script>

<style scoped>

</style>
