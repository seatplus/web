<template>
  <Link
    v-if="hasContent"
    class="justify-self-end"
    :href="$route('character.item', {item_id: entry.item_id, character_id: entry.owner.character_id})"
    preserve-state
    preserve-scroll
  >
    <CompactAssetListTemplate
      :entry="entry"
      :even="even"
    />
  </Link>
  <CompactAssetListTemplate
    v-else
    :entry="entry"
    :even="even"
  />
</template>

<script>
import CompactAssetListTemplate from "./CompactAssetListTemplate";
import { Link } from '@inertiajs/inertia-vue3'
export default {
    name: "CompactAssetListElement",
    components: {CompactAssetListTemplate, Link},
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