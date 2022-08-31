<template>
  <div class="mb-4">
    This corporation accepts recruits on a character level. This means every character must apply individually and a possible set of required scopes is only applied to the enlisted characters of yours.
  </div>
  <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
    <li
      v-for="character in ownedCharacters"
      :key="character.character_id"
      class="pl-3 pr-4 py-3 flex items-center justify-between text-sm"
    >
      <div class="flex-1 flex items-center">
        <EveImage
          :object="character"
          :size="256"
          tailwind_class="h-5 w-5 rounded-full"
        />
        <span class="ml-2 flex-1 truncate">
          {{ character.name }}
        </span>
      </div>
      <div class="ml-4 flex-shrink-0">
        <button
          v-if="hasApplied(character.character_id)"
          class="inline-flex items-center px-1.5 py-1 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red focus:border-red-700"
          @click="remove(character.character_id)"
        >
          <UserRemoveIcon
            class="-ml-0.5 mr-1 h-4 w-4"
            aria-hidden="true"
          />
          Remove Application
        </button>
        <button
          v-else
          class="inline-flex items-center px-1.5 py-1 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="apply(character.character_id)"
        >
          <UserAddIcon
            class="-ml-0.5 mr-1 h-4 w-4"
            aria-hidden="true"
          />
          Apply
        </button>
      </div>
    </li>
  </ul>
</template>

<script>
import EveImage from "@/Shared/EveImage.vue"
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import { Inertia } from '@inertiajs/inertia'
import {UserAddIcon, UserRemoveIcon} from "@heroicons/vue/solid";

export default {
    name: "CharacterApplication",
    components: {EveImage, UserAddIcon, UserRemoveIcon},
    props: {
        enlistment: {
            type: Object,
            required: true
        },
        applicationResults: {
            type: Array,
            required: true
        }
    },
    setup(props) {

        const applications = ref(props.applicationResults)

        const ownedCharacters = computed(() => usePage().props.value.user.data.characters)
        const applicants = computed(() => {

            if(!hasApplications.value || props.enlistment.type !== 'character')
                return []

            return _.map(applications.value, (application) => _.get(application, 'applicationable')) //applicationResults.results.value

        })
        const hasApplications = computed(() =>  !!applications.value) //applicationResults.results.value
        
        const hasApplied = (character_id) =>  _.findIndex(applicants.value, {character_id: character_id}) > -1

        const apply = (character_id) => Inertia.post(route('post.application'), {
            corporation_id: props.enlistment.corporation_id,
            character_id: character_id
        }, {
            onSuccess: () => applications.value.push({
                applicationable: {
                    character_id: character_id
                }
            }),
            preserveState: true
        })

        const remove = (character_id) => Inertia.delete(route('delete.character.application', character_id), {
            preserveState: true,
            onSuccess: () => _.remove(applications.value, function (application) {
                return _.isEqual(application.applicationable.character_id, character_id)
            })
        })

        
        return {
            hasApplied,
            ownedCharacters,
            apply,
            remove
        }
    }
}
</script>

<style scoped>

</style>
