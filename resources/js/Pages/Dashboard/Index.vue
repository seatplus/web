<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

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
import Layout from "../../Shared/SidebarLayout/Layout";
import PageHeader from "../../Shared/Layout/PageHeader"
import Enlistments from "./Enlistments"
import Characters from "./Characters"

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
