<template>
  <BarWithUnderline
    :tabs="tabs"
    @select="changeActiveTab"
  />
  <AssetsComponent
    :key="activeTabId"
    :parameters="parameters"
    context="recruitment"
    :compact="true"
  />
</template>

<script setup>
import BarWithUnderline from "@/Shared/Layout/Tabs/BarWithUnderline.vue";
import {computed, ref} from "vue";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent.vue";
import {merge} from "lodash";

const props = defineProps({
    characterIds: {
        required: true,
        type: Array
    },
    watchlist: {
        required: true,
        type: Object
    }
})

const activeTabId = ref(1)

const tabs = computed(() => {
    // check if the watchlist prop is not empty
    let has_watchlist =  Object.values(props.watchlist).some(array => array.length > 0);

    let raw_tabs = [
        'All Assets',
        'Assets in Unknown Locations'
    ]

    // iff has_watchlist is true, add the watchlist tab at the beginning of the array
    raw_tabs =  has_watchlist ? ['Watchlisted Assets', ...raw_tabs] : raw_tabs

    return raw_tabs.map((name, index) => ({id: index+1, name: name}))
})


const changeActiveTab = (tab) => activeTabId.value = tab.id

const parameters = computed(() => {
    return [
        merge({character_ids: props.characterIds}, props.watchlist),
        { character_ids: props.characterIds },
        { character_ids: props.characterIds, only_unknown_locations: true},
    ][activeTabId.value-1]
})

</script>
