<template>
  <BarWithUnderline
    :tabs="tabs"
    @select="changeActiveTab"
  />
  <AssetsComponent
    v-for="tab in tabs"
    v-show="tab.id === activeTabId"
    :key="tab.id"
    :parameters="tab.parameters"
    context="recruitment"
    :compact="true"
  />
</template>

<script>
import BarWithUnderline from "@/Shared/Layout/Tabs/BarWithUnderline";
import {computed, ref} from "vue";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";

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
            let {systems, regions, types, groups, categories} = props.watchlist

            //return _.chain([systems, regions, types, groups, categories]).flattenDeep().values().length >0
            return _.filter([
                ...(_.isArray(systems) ? systems : []),
                ...(_.isArray(regions) ? regions : []),
                ...(_.isArray(types) ? types : []),
                ...(_.isArray(groups) ? groups : []),
                ...(_.isArray(categories) ? categories : [])
            ]).length > 0
        }

        const changeActiveTab = (tab) => activeTabId.value = tab.id

        const tabs = computed(() => {

            let cartesian_array = [
                { parameters: _.merge({character_ids: props.characterIds}, props.watchlist) },
                { parameters: {character_ids: props.characterIds} },
                { parameters: {character_ids: props.characterIds, withUnknownLocations:true} },
            ]

            let tabs = _.merge(raw_tabs, cartesian_array)

            return hasWatchlist() ? tabs : _.reject(tabs, {id: 1})
        })

        return {
            tabs,
            activeTabId,
            changeActiveTab
        }
    }
}
</script>