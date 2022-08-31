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
import HeaderButton from "@/Shared/Layout/HeaderButton.vue";
import SlideOver from "@/Shared/Layout/SlideOver.vue";
import DispatchUpdate from "./DispatchUpdate.vue";
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
      return this.route('manual_job.entities', this.dispatch_transfer_object)
    }
  }
}
</script>

<style scoped>

</style>
