<template>
  <inertia-link
    v-if="hasContent"
    class="justify-self-end"
    :href="$route('character.item', entry.item_id)"
    preserve-state
    preserve-scroll
  >
    <CompactAssetListTemplate
      :entry="entry"
      :even="even"
    />
  </inertia-link>
  <CompactAssetListTemplate
    v-else
    :entry="entry"
    :even="even"
  />
</template>

<script>
import CompactAssetListTemplate from "./CompactAssetListTemplate";
export default {
    name: "CompactAssetListElement",
    components: {CompactAssetListTemplate},
    props: {
        entry: {
            required: true,
            type: Object
        },
        even: {
            required: true,
            type: Number
        }
    },
    computed: {
        hasContent() {
            return _.size(this.entry.content) > 0
        },
    }
}
</script>

<style scoped>

</style>