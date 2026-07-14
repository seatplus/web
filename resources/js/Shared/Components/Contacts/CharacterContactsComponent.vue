<template>
  <!-- This example requires Tailwind CSS v2.0+ -->

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
        <li
          v-if="loading"
          class="flex justify-center py-6"
        >
          <svg
            class="animate-spin h-6 w-6 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
        </li>
        <li
          v-else-if="!contacts.length"
          class="text-center text-sm text-gray-500 py-6"
        >
          no entries
        </li>
        <CharacterContactsRowComponent
          v-for="(entry, index) in contacts"
          :key="entry.contact_id"
          :even="index%2"
          :entry="entry"
        />
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>

import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import CharacterContactsRowComponent from "./CharacterContactsRowComponent.vue";
import {computed, onMounted, ref} from "vue";
import SimpleInlineList from "../../Layout/SimpleInlineList.vue";
import { postJson } from "@/Functions/http";
import { getContacts } from "@/actions/Seatplus/Web/Http/Controllers/Character/ContactsController";

export default {
    name: "CharacterContactsComponent",
    components: {
        SimpleInlineList,
        CharacterContactsRowComponent,
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
    },
    setup(props) {

        const contacts_raw = ref([])
        const loading = ref(true)

        // The contacts endpoint is a POST (it takes the target corporation to compute relative
        // standings) and returns the full, unpaginated list. Fetch it once, axios/Ziggy-free.
        onMounted(async () => {
            try {
                const response = await postJson(getContacts.url(props.character.character_id), {
                    target_corporation_id: props.corporation_id,
                })
                contacts_raw.value = response?.data ?? []
            } finally {
                loading.value = false
            }
        })

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
            contacts_raw,
            loading,
        }
    }
}
</script>

<style scoped>

</style>
