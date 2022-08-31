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
    <div
      class="px-3 py-4 sm:py-1 self-center whitespace-normal"
      :class="canReview ? 'sm:col-span-8' : 'sm:col-span-9'"
    >
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Characters
      </label>
      <div class="flex gap-x-2 flex-wrap">
        <CharacterComplianceElement
          v-for="character in [...nonCompliantCharacters, ...compliantCharacters]"
          :key="character.character_id"
          :character="character"
        />
      </div>
    </div>
    <div
      v-if="canReview"
      class="px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-1"
    >
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Review
      </label>
      <div class="flex gap-x-2 flex-wrap">
        <Button
          button-size="xs"
          :href="route('corporation.review.user', {'corporation_id': corporationId, 'user': user.id})"
        >
          Review
        </Button>
      </div>
    </div>
  </li>
</template>

<script>
import {computed} from "vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import CharacterComplianceElement from "./CharacterComplianceElement.vue";
import Button from "@/Shared/Layout/Button.vue";

export default {
    name: "MemberComplianceListElement",
    components: {Button, CharacterComplianceElement, EntityBlock},
    props: {
        even: {
            required: true,
            type: Boolean
        },
        user: {
            required: true,
            type: Object
        },
        corporationId: {
            required: true,
            type: Number
        },
        canReview: {
            required: true,
            type: Boolean
        }
    },
    setup(props) {
        const characters = computed(() => _.sortBy(props.user.characters, ['name']))

        const nonCompliantCharacters = computed(() => _.filter(characters.value, (character) => {

            let missingScopes = _.isObject(character.missing_scopes) ? Object.values(character.missing_scopes) : character.missing_scopes

            return missingScopes.length >0
        }))

        const compliantCharacters = computed(() => _.filter(characters.value, (character) => character.missing_scopes.length === 0))
        //const canReview = computed(() => usePage().props.value.canReview)

        return {
            characters,
            nonCompliantCharacters,
            compliantCharacters
        }
    }
}
</script>

<style scoped>

</style>