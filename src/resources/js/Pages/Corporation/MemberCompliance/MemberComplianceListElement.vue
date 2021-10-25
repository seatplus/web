<template>
  <li
    :class="[even ? 'bg-gray-50' : 'bg-white']"
    class="grid grid-cols-1 sm:grid-cols-12 gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-3">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Main Character
      </label>
      <EntityBlock
        :entity="user.main_character"
        :image-size="10"
      />
    </div>
    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-9">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Main Character
      </label>
      <div class="flex gap-x-2 flex-wrap">
        <CharacterComplianceElement
          v-for="character in [...nonCompliantCharacters, ...compliantCharacters]"
          :key="character.character_id"
          :character="character"
        />
      </div>
    </div>
  </li>
</template>

<script>
import {computed} from "vue";
import EntityBlock from "../../../Shared/Layout/Eve/EntityBlock";
import CharacterComplianceElement from "./CharacterComplianceElement";

export default {
    name: "MemberComplianceListElement",
    components: {CharacterComplianceElement, EntityBlock},
    props: {
        even: {
            required: true,
            type: Boolean
        },
        user: {
            required: true,
            type: Object
        }
    },
    setup(props) {
        const characters = computed(() => _.sortBy(props.user.characters, ['name']))

        const nonCompliantCharacters = computed(() => _.filter(characters.value, (character) => {

            let missingScopes = _.isObject(character.missing_scopes) ? Object.values(character.missing_scopes) : character.missing_scopes

            return missingScopes.length >0
        }))

        const compliantCharacters = computed(() => _.filter(characters.value, (character) => character.missing_scopes.length === 0))

        return {
            characters,
            nonCompliantCharacters,
            compliantCharacters,
        }
    }
}
</script>

<style scoped>

</style>