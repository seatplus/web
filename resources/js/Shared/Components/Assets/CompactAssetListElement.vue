<template>
  <CompactAssetListTemplate
    :entry="entry"
    :even="even"
  />
</template>

<script setup>
import CompactAssetListTemplate from "./CompactAssetListTemplate.vue";

defineProps({
    entry: {
        required: true,
        type: Object
    },
    even: {
        required: true,
        type: Number
    }
});
</script>
