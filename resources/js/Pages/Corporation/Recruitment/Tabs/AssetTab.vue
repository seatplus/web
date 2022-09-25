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

<script>
import BarWithUnderline from "@/Shared/Layout/Tabs/BarWithUnderline.vue";
import {computed, ref} from "vue";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent.vue";

let raw_tabs = [
    {id: 1, name: 'Watchlisted Assets'},
    {id: 2, name: 'All Assets'},
    {id: 3, name: 'Assets in Unknown Locations'}
]

export default {
    name: "AssetTab",
    components: {AssetsComponent, BarWithUnderline},
    props: {
        characterIds: {
            required: true,
            type: Array
        },
        watchlist: {
            required: true,
            type: Object
        }
    },
    setup(props) {

        const activeTabId = ref(hasWatchlist() ? 1 : 2)

        function hasWatchlist () {

            return _.flattenDeep(Object.values(props.watchlist)).length > 0
        }

        const changeActiveTab = (tab) => activeTabId.value = tab.id

        const parameters = computed(() => {
            return [
                _.merge({character_ids: props.characterIds}, props.watchlist),
                { character_ids: props.characterIds },
                { character_ids: props.characterIds, withUnknownLocations:true},
            ][activeTabId.value-1]
        })

        return {
            tabs: raw_tabs,
            activeTabId,
            parameters,
            changeActiveTab
        }
    }
}
</script>