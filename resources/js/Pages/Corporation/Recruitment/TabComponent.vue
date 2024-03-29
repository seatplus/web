<template>
  <div class="space-y-4">
    <div>
      <div class="sm:hidden">
        <label
          for="tabs"
          class="sr-only"
        >
          Select a tab
        </label>
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
    <LogTab
      v-if="isActive('Log') && application"
      :application="application"
    />
    <div
      v-if="isActive('Assets')"
      class="space-y-4"
    >
      <AssetTab
        :character-ids="characterIds"
        :watchlist="watchlist"
      />
    </div>
    <div
      v-if="isActive('Contracts')"
      class="space-y-4"
    >
      <ContractTab
        :watchlist="watchlist"
        :character-ids="characterIds"
      />
    </div>
    <div
      v-if="isActive('Wallets')"
      class="space-y-4"
    >
      <WalletTab :character-ids="characterIds" />
    </div>
    <div
      v-if="isActive('Contacts')"
      class="space-y-4"
    >
      <CharacterContactsComponent
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
    <div
      v-if="isActive('Skills')"
      class="space-y-4"
    >
      <SkillsComponent
        v-for="character in recruit.characters"
        :key="'character.skills:' + character.character_id"
        :character-id="character.character_id"
      />
    </div>
    <div
      v-if="isActive('Mails')"
    >
      <MobileMailList
        :character-ids="characterIds"
      />
    </div>
  </div>
</template>

<script>
import CorporationHistoryComponent from "@/Shared/Components/Character/CorporationHistoryComponent.vue";
import SkillsComponent from "@/Shared/Components/Skills/SkillsComponent.vue";
import MobileMailList from "@/Shared/Components/Mails/MobileMailList.vue";
import CharacterContactsComponent from "@/Shared/Components/Contacts/CharacterContactsComponent.vue";
import AssetTab from "./Tabs/AssetTab.vue";
import ContractTab from "./Tabs/ContractTab.vue";
import LogTab from "@/Pages/Corporation/Recruitment/Tabs/LogTab.vue";
import WalletTab from "./Tabs/WalletTab.vue";

const tabs = [
    'Log',
    'Assets',
    'Contracts',
    'Wallets',
    'Contacts',
    'Corporation History',
    'Skills',
    'Mails'
]

export default {
    name: "TabComponent",
    components: {
        WalletTab,
        LogTab,
        ContractTab,
        AssetTab,
        CharacterContactsComponent,
        MobileMailList,
        SkillsComponent,
        CorporationHistoryComponent
        },
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
            required: false
        },
        application: {
            type: Object,
            required: false
        }
    },
    setup() {
        return {
            tabs
        }
    },
    data() {
        return {
            active_element: 'Log',
        }
    },
    computed: {
        characterIds() {
            return _.map(this.recruit.characters, character => character.character_id)
        },
        /*targetCorporation() {
            return this.application.corporation
        }*/
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