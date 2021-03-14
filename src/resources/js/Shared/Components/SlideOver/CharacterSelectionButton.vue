<template>
  <span class="inline-block relative">
    <HeaderButton
      :secondary="true"
      @click="openSlideOver()"
    >
      Select Characters
    </HeaderButton>
    <span
      v-if="has_selected"
      class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-yellow-400"
    />
  </span>
  <teleport to="#destination">
    <SlideOver v-model:open="open">
      <CharacterSelection :url="url" />
    </SlideOver>
  </teleport>
</template>

<script>
import HeaderButton from "@/Shared/Layout/HeaderButton";
import SlideOver from "../../Layout/SlideOver";
import CharacterSelection from "./CharacterSelection";
export default {
    name: "CharacterSelectionButton",
    components: {CharacterSelection, SlideOver, HeaderButton},
    computed: {
        has_selected() {
            let character_ids = _.get(this.$route().params, 'character_ids')

            return !!character_ids
        },
        url() {
          return this.$route('get.affiliated.characters', this.dispatch_transfer_object.permission)
        }
    },
  data() {
      return {
        open: false,
        dispatch_transfer_object: this.$page.props.dispatch_transfer_object
      }
  },
    methods: {
        openSlideOver() {
          this.open = true
        },
    },
}
</script>

<style scoped>

</style>
