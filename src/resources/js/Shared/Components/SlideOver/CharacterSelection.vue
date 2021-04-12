<template>
  <ul
    ref="scrollComponent"
    class="divide-y divide-gray-200 overflow-y-auto border-t"
  >
    <SelectionEntity
      v-for="character in result"
      :key="character.character_id"
      v-model="selected_character_ids"
      :entity="character"
    />
  </ul>
  <div ref="scrollComponent"></div>
</template>

<script>
import SelectionEntity from "./SelectionEntity";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
  name: "CharacterSelection",
  components: {SelectionEntity},
  props: {
    permission: {
      required: true,
      type: String
    }
  },
  setup(props) {

    return useInfinityScrolling('get.affiliated.characters', props.permission)
  },
  data() {
    return {
      initial_ids: [],
      selected_character_ids: []
    }
  },
  computed: {
    changed() {
      return !_.isEqual(this.initial_ids, this.selected_character_ids)
    }
  },
  beforeMount() {

    let character_ids = _.get(this.$route().params, 'character_ids')

    if(!character_ids)
      return

    this.selected_character_ids = _.map(character_ids, (id) => parseInt(id))
    this.initial_ids = _.map(character_ids, (id) => parseInt(id))

  },
  beforeUnmount() {

    if(!this.changed)
      return

    let route = this.$route().current()

    if(_.isEmpty(this.selected_character_ids))
      return this.$inertia.get(this.$route(route))

    this.$inertia.get(this.$route(route, {_query: {character_ids: this.selected_character_ids}}))
  },
}
</script>

<style scoped>

</style>
