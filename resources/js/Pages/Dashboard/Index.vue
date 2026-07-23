<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <div class="divide-y">
      <Characters
        :characters="characters"
        class="py-4"
      />
    </div>
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import Characters from "./Characters.vue"

defineProps({
    characters: {
        type: Array,
        default: () => []
    },
});

const pageTitle = 'Home'
</script>

<style scoped>

</style>
