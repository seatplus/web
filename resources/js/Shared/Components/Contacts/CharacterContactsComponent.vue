<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="grow"
          :entity="character"
        />
        <div>
          <div class="flex-none text-right text-sm text-gray-500">
            Contacts
          </div>
          <SimpleInlineList
            :key="character.character_id"
            v-model="selected_filter"
            legend="Filter contacts"
            :options="options"
          />
        </div>
      </div>
    </template>
    <div class="relative max-h-96 overflow-y-auto">
      <p
        v-if="filteredContacts.length === 0"
        class="py-12 text-center text-sm text-gray-500"
      >
        No contacts
      </p>

      <StickyHeaderTable
        v-else
        :header-titles="headerTitles"
      >
        <template #default="{ countColumns, columns }">
          <CharacterContactsRowComponent
            v-for="entry in filteredContacts"
            :key="entry.contact_id"
            :entry="entry"
            :columns="columns"
            :number-columns="countColumns"
          />
        </template>
      </StickyHeaderTable>
    </div>
  </CardWithHeader>
</template>

<script setup>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import CharacterContactsRowComponent from "./CharacterContactsRowComponent.vue";
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable.vue";
import SimpleInlineList from "../../Layout/SimpleInlineList.vue";
import { computed, ref } from "vue";

const props = defineProps({
    character: {
        required: true,
        type: Object
    },
    contacts: {
        type: Array,
        default: () => []
    },
});

const selected_filter = ref('')
const options = [
    {id: 'all', title: 'All contacts'},
    {id: 'standing', title: 'Only With Standing Offset'},
]

const headerTitles = [
    {title: 'Contact', columnSpan: 2},
    {title: 'Labels', columnSpan: 1},
    {title: 'Standing', columnSpan: 1},
    {title: 'Corporation standing', columnSpan: 1},
    {title: 'Alliance standing', columnSpan: 1},
]

const diff = (a,b) => a > b ? a - b : b - a

const filteredContacts = computed(() => {

    let unsortedContacts = props.contacts

    if(selected_filter.value === 'wofaction') {
        unsortedContacts = _.filter(props.contacts, {contact_type: 'faction'})
    }

    if(selected_filter.value === 'standing') {
        unsortedContacts =  _.filter(props.contacts, (contact) => {
            if(_.isNil(contact.corporation_standing) && _.isNil(contact.alliance_standing)) {
                return false
            }

            let standing = contact.standing

            if(standing === 0) {
                return false
            }

            let corp_standing = contact.corporation_standing != null ? contact.corporation_standing : 0
            let alliance_standing = contact.alliance_standing != null ? contact.alliance_standing : 0

            return !((diff(corp_standing,standing) === 0) || (diff(alliance_standing,standing) === 0));

        })
    }

    return _.chain(unsortedContacts)
        .sortBy(['standing', 'corporation_standing', 'alliance_standing'])
        .reverse()
        .value()
})
</script>

<style scoped>

</style>
