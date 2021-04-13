<template>
  <span class="inline-block relative">
    <HeaderButton
      :secondary="true"
      @click="openSlideOver"
    >
      Select {{ type.replace(/^\w/, c => c.toUpperCase()) }}
    </HeaderButton>
    <span
      v-if="has_selected"
      class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-yellow-400"
    />
  </span>
  <teleport to="#destination">
    <SlideOver v-model:open="open">
      <template #title>
        Select {{ type }}
      </template>
      <EntitySelection
        :dispatch-transfer-object="dispatchTransferObject"
        :type="type"
      />
    </SlideOver>
  </teleport>
</template>

<script>
import HeaderButton from "@/Shared/Layout/HeaderButton";
import SlideOver from "../../Layout/SlideOver";
import EntitySelection from "./EntitySelection";
export default {
    name: "EntitySelectionButton",
    components: {EntitySelection, SlideOver, HeaderButton},
    props: {
        type: {
            type: String,
            default: () => 'character'
        }
    },
    data() {
        return {
            open: false
        }
    },
    computed: {
        has_selected() {
            let ids = _.get(this.$route().params, `${this.type}_ids`)

            return !!ids
        },
        dispatchTransferObject() {
            return this.$page.props.dispatch_transfer_object != null ? this.$page.props.dispatch_transfer_object : this.$page.props.dispatchTransferObject
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
