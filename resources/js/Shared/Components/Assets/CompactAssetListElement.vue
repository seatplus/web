<template>
  <CompactAssetListTemplate
    :entry="entry"
    :even="even"
  />
</template>

<script>
import CompactAssetListTemplate from "./CompactAssetListTemplate.vue";

export default {
    name: "CompactAssetListElement",
    components: { CompactAssetListTemplate },
    props: {
        entry: {
            required: true,
            type: Object
        },
        even: {
            required: true,
            type: Number
        }
    }
}
</script>
