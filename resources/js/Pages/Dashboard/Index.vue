<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    Maybe i have found it: no? now? this looks good

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <Characters
      :characters="characters"
      class="mb-4"
    />

    <Enlistments :key="timestamp" />
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
            type: Array
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
