<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <div class="divide-y">
      <Characters
        :characters="characters"
        class="py-4"
      />

      <Enlistments
        :key="timestamp"
        class="py-4"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import Enlistments from "./Enlistments.vue"
import Characters from "./Characters.vue"

export default {
    name: "Index",
    components: {Characters, Enlistments, PageHeader},
    props: {
        characters: {
            type: Array,
            default: () => []
        },
    },
    setup() {

        const timestamp = +new Date();

        return {
            pageTitle: 'Home',
            timestamp
        }
    },
}
</script>

<style scoped>

</style>
