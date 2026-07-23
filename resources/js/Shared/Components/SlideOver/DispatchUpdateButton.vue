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

<script setup>
import { ref } from "vue";
import HeaderButton from "@/Shared/Layout/HeaderButton.vue";
import SlideOver from "@/Shared/Layout/SlideOver.vue";
import DispatchUpdate from "./DispatchUpdate.vue";

const open = ref(false)
</script>

<style scoped>

</style>
