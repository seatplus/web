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
import HeaderButton from "@/Shared/Layout/HeaderButton";
import SlideOver from "@/Shared/Layout/SlideOver";
import DispatchUpdate from "./DispatchUpdate";
export default {
  name: "DispatchUpdateButton",
  components: {DispatchUpdate, SlideOver, HeaderButton},
  computed: {
    url() {
      return this.$route('manual_job.entities', this.dispatch_transfer_object)
    }
  },
  data() {
    return {
      open: false
    }
  }
}
</script>

<style scoped>

</style>
