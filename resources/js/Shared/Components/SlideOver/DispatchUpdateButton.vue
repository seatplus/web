<template>
  <HeaderButton @click="open = true">
    Update
  </HeaderButton>
  <teleport to="#destination">
    <SlideOver v-model:open="open">
      <template #title>
        Dispatch Update Job
      </template>
      <DispatchUpdate />
    </SlideOver>
  </teleport>
</template>

<script>
import HeaderButton from "../../Layout/HeaderButton";
import SlideOver from "../../Layout/SlideOver";
import DispatchUpdate from "./DispatchUpdate";
export default {
  name: "DispatchUpdateButton",
  components: {DispatchUpdate, SlideOver, HeaderButton},
  data() {
    return {
      open: false
    }
  },
  computed: {
    url() {
      return this.$route('manual_job.entities', this.dispatch_transfer_object)
    }
  }
}
</script>

<style scoped>

</style>
