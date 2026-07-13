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
import { defineAsyncComponent } from "vue";
import CompactAssetListTemplate from "./CompactAssetListTemplate.vue";

export default {
    name: "CompactAssetListElement",
    // AssetContentsLink is async to break the ItemList ↔ AssetContentsLink import cycle.
    components: { CompactAssetListTemplate, AssetContentsLink: defineAsyncComponent(() => import("./AssetContentsLink.vue")) },
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
