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
          <option>Assets</option>

          <option>Contracts</option>

          <option>Wallets</option>

          <option>Contacts</option>
        </select>
      </div>
      <div class="hidden sm:block">
        <div class="flex space-x-4">
          <!-- Current: "bg-indigo-100 text-indigo-700", Default: "text-gray-500 hover:text-gray-700" -->
          <div
            class="px-3 py-2 font-medium text-sm rounded-md cursor-pointer"
            :class="isActive('Assets') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
            @click="active_element = 'Assets'"
          >
            Assets
          </div>

          <div
            class="px-3 py-2 font-medium text-sm rounded-md cursor-pointer"
            :class="isActive('Contracts') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
            @click="active_element = 'Contracts'"
          >
            Contracts
          </div>

          <div
            class="px-3 py-2 font-medium text-sm rounded-md cursor-pointer"
            :class="isActive('Wallets') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
            @click="active_element = 'Wallets'"
          >
            Wallets
          </div>

          <div
            class="px-3 py-2 font-medium text-sm rounded-md cursor-pointer"
            :class="isActive('Contacts') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
            @click="active_element = 'Contacts'"
          >
            Contacts
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
      />
      <AssetsComponent
        :parameters="asset_params"
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
      <WalletJournalComponent
        v-for="character in recruit.characters"
        :id="character.character_id"
        :key="'wallet.journal:' + character.character_id"
      />
      <WalletTransactionComponent
        v-for="character in recruit.characters"
        :id="character.character_id"
        :key="'wallet.transaction:' + character.character_id"
      />
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
  </div>
</template>

<script>
import CharacterContactPanel from "@/Shared/Components/CharacterContactPanel";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent";
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";
import ContractComponent from "@/Shared/Components/Contracts/ContractComponent";

export default {
    name: "TabComponent",
    components: {ContractComponent,
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