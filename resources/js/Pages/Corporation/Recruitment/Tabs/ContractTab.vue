<template>
  <BarWithUnderline
    v-if="hasWatchlist"
    :tabs="tabs"
    @select="changeActiveTab"
  />
  <ContractComponent
    v-for="character_id in characterIds"
    :id="character_id"
    :key="`${character_id}:${activeTabId}`"
    :watchlist="activeTabId === 1 ? watchlist : {}"
  />
</template>

<script>
import BarWithUnderline from "../../../../Shared/Layout/Tabs/BarWithUnderline";
import {computed, ref} from "vue";
import ContractComponent from "../../../../Shared/Components/Contracts/ContractComponent";

let raw_tabs = [
    {id: 1, name: 'Watchlisted Contracts'},
    {id: 2, name: 'All Contracts'},
]

export default {
    name: "ContractTab",
    components: {ContractComponent, BarWithUnderline},
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

        const changeActiveTab = (tab) => activeTabId.value = tab.id;

        const hasWatchlist = computed(() => _.flattenDeep(Object.values(props.watchlist)).length > 0)

        const activeTabId = ref(hasWatchlist.value ? 1 : 2)

        return {
            tabs: raw_tabs,
            changeActiveTab,
            hasWatchlist,
            activeTabId
        }
    }
}
  </script>