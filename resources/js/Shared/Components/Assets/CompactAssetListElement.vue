<template>
  <AssetContentsLink
    v-if="hasContent"
    class="justify-self-end"
    :character-id="entry.owner_id"
    :item-id="entry.item_id"
  >
    <CompactAssetListTemplate
      :entry="entry"
      :even="even"
    />
  </AssetContentsLink>
  <CompactAssetListTemplate
    v-else
    :entry="entry"
    :even="even"
  />
</template>

<script>
import CompactAssetListTemplate from "./CompactAssetListTemplate.vue";
import AssetContentsLink from "./AssetContentsLink.vue";
export default {
    name: "CompactAssetListElement",
    components: { CompactAssetListTemplate, AssetContentsLink },
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
            return (this.entry.content_count ?? _.size(this.entry.content)) > 0
        },
    }
}
</script>
