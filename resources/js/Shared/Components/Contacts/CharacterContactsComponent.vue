<template>
  <!-- This example requires Tailwind CSS v2.0+ -->

  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="flex-grow"
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
      <div class="hidden sm:grid sm:grid-cols-6 sm:gap-x-0 sm:gap-y-0.5 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Contact
        </div>
        <div class="px-6 sm:px-3 py-1">
          Labels
        </div>
        <div class="px-6 sm:px-3 py-1">
          Standing
        </div>
        <div class="px-6 sm:px-3 py-1">
          Corporation standing
        </div>
        <div class="px-6 sm:px-3 py-1">
          Alliance standing
        </div>
      </div>
      <ul class="relative z-0 divide-y divide-gray-200">
        <CompleteLoadingHelper
          route-name="character.contacts.detail"
          :params="{character_id: character.character_id}"
          :form-data="{corporation_id: corporation_id, alliance_id: alliance_id}"
          @results="(result) => contacts_raw = result"
        >
          <CharacterContactsRowComponent
            v-for="(entry, index) in contacts"
            :key="entry.contact_id"
            :even="index%2"
            :entry="entry"
          />
        </CompleteLoadingHelper>
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>

import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import CompleteLoadingHelper from "../../Layout/CompleteLoadingHelper.vue";
import CharacterContactsRowComponent from "./CharacterContactsRowComponent.vue";
import {computed, ref} from "vue";
import SimpleInlineList from "../../Layout/SimpleInlineList.vue";

export default {
    name: "CharacterContactsComponent",
    components: {
        SimpleInlineList,
        CharacterContactsRowComponent,
        CompleteLoadingHelper,
        CardWithHeader, EntityBlock
    },
    props: {
        character: {
            required: true,
            type: Object
        },
        corporation_id: {
            required: true,
            type: Number
        },
        alliance_id: {
            required: false,
            type: Number
        },
    },
    setup() {

        const contacts_raw = ref([])
        const selected_filter = ref('')
        const options = [
            {id: 'all', title: 'All contacts'},
            {id: 'standing', title: 'Only With Standing Offset'},
        ]

        const diff = (a,b) => a > b ? a - b : b - a

        const contacts = computed(() => {

            let unsortedContacts = contacts_raw.value

            if(selected_filter.value === 'wofaction') {
                unsortedContacts = _.filter(contacts_raw.value, {contact_type: 'faction'})
            }

            if(selected_filter.value === 'standing') {
                unsortedContacts =  _.filter(contacts_raw.value, (contact) => {
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

        return {
            contacts,
            options,
            selected_filter,
            contacts_raw
        }
    }
}
</script>

<style scoped>

</style>
