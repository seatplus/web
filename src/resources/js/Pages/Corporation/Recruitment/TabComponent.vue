<template>
  <div class="space-y-4">
    <div>
      <div class="sm:hidden">
        <label
          for="tabs"
          class="sr-only"
        >Select a tab</label>
        <select
          id="tabs"
          v-model="active_element"
          name="tabs"
          class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
        >
          <option
            v-for="tab in tabs"
            :key="tab"
          >
            {{ tab }}
          </option>
        </select>
      </div>
      <div class="hidden sm:block">
        <div class="flex space-x-4">
          <div
            v-for="tab in tabs"
            :key="tab"
            class="px-3 py-2 font-medium text-sm rounded-md cursor-pointer"
            :class="isActive(tab) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
            @click="active_element = tab"
          >
            {{ tab }}
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="isActive('Assets')"
      class="space-y-4"
    >
      <AssetsComponent
        :parameters="unknown_asset_params"
        context="recruitment"
        :compact="true"
      />
      <AssetsComponent
        :parameters="asset_params"
        context="recruitment"
        :compact="true"
      />
    </div>
    <div
      v-if="isActive('Contracts')"
      class="space-y-4"
    >
      <ContractComponent
        v-for="character in recruit.characters"
        :id="character.character_id"
        :key="`contract.component:${character.character_id}`"
      />
    </div>
    <div
      v-if="isActive('Wallets')"
      class="space-y-4"
    >
      <div
        v-for="character in recruit.characters"
        :key="`wallet component ${character.character_id}`"
        class="space-y-4"
      >
        <WalletJournalBalanceChart
          :id="character.character_id"
        />
        <WalletJournalComponent
          :id="character.character_id"
        />
        <WalletTransactionComponent
          :id="character.character_id"
        />
      </div>
    </div>
    <div
      v-if="isActive('Contacts')"
      class="space-y-4"
    >
      <CharacterContactPanel
        v-for="character in recruit.characters"
        :key="'character.contact:' + character.character_id"
        :character="character"
        :corporation_id="targetCorporation.corporation_id"
        :alliance_id="targetCorporation.alliance_id"
      />
    </div>
    <div
      v-if="isActive('Corporation History')"
      class="space-y-4"
    >
      <CorporationHistoryComponent
        v-for="character in recruit.characters"
        :key="'corporation.history:' + character.character_id"
        :character="character"
      />
    </div>
  </div>
</template>

<script>
import CharacterContactPanel from "@/Shared/Components/CharacterContactPanel";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent";
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";
import ContractComponent from "@/Shared/Components/Contracts/ContractComponent";
import WalletJournalBalanceChart from "@/Shared/Components/Wallet/Journal/WalletJournalBalanceChart";
import CorporationHistoryComponent from "../../../Shared/Components/Character/CorporationHistoryComponent";

const tabs = ['Assets', 'Contracts', 'Wallets', 'Contacts', 'Corporation History']

export default {
    name: "TabComponent",
    components: {
        CorporationHistoryComponent,
        WalletJournalBalanceChart, ContractComponent,
        AssetsComponent,
        WalletJournalComponent,
        WalletTransactionComponent, CharacterContactPanel},
    props: {
        recruit: {
            type: Object,
            required: true
        },
        watchlist: {
            type: Object,
            required: true
        },
        targetCorporation: {
            type: Object,
            required: true
        }
    },
    setup() {
        return {
            tabs
        }
    },
    data() {
        return {
            active_element: 'Assets',

        }
    },
    computed: {
        asset_params() {
            return {
                character_ids: _.map(this.recruit.characters, character => character.character_id),
                regions: this.watchlist.regions,
                systems: this.watchlist.systems
            }
        },
        unknown_asset_params() {
            return {
                character_ids: _.map(this.recruit.characters, character => character.character_id),
                withUnknownLocations: true
            }
        }
    },
    methods: {
        isActive(entry) {
            return _.isEqual(entry, this.active_element)
        },
        select(entry) {
            this.active_element = entry
        }
    }
}
</script>

<style scoped>

</style>