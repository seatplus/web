<template>
  <CharacterComplianceElement
    v-for="character of filteredResults"
    :key="character.character.character_id"
    :character="character"
  />
  <div ref="scrollComponent"></div>
</template>

<script>
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
import CharacterComplianceElement from "./CharacterComplianceElement";

export default {
  name: "MissingCharacters",
  components: {CharacterComplianceElement},
  props: {
    parameters: {
      required: true,
      type: Object
    }
  },
  setup(props) {

    return useInfinityScrolling('missing.characters.compliance', props.parameters)
  },
  computed: {
    filteredResults() {
      return _.filter(this.result, (res) => !_.isEmpty(res) )
    }
  }
}
</script>

<style scoped>

</style>
