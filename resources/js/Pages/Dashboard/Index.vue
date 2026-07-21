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

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import Characters from "./Characters.vue"

export default {
    name: "Index",
    components: {Characters, PageHeader},
    props: {
        characters: {
            type: Array,
            default: () => []
        },
    },
    setup() {

        return {
            pageTitle: 'Home',
        }
    },
}
</script>

<style scoped>

</style>
