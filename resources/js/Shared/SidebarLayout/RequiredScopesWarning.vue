<template>
  <div v-if="missing_characters_scopes.length >0 ">
    <Disclosure
      v-slot="{ open }"
      :default-open="state"
      class="bg-amber-100 shadow sm:rounded-lg"
    >
      <div class="px-4 py-5 sm:p-6">
        <DisclosureButton
          class="flex w-full justify-between"
          @click="flipState"
        >
          <span>
            <h3 class="text-lg leading-6 font-medium text-amber-900 text-left">
              Missing scopes warning
            </h3>
            <p class="text-base leading-6 font-medium text-amber-900">
              Some characters are missing some scopes on their refresh_token for seatplus to fetch information from esi.
            </p>
          </span>

          <ChevronRightIcon
            :class="open && 'rotate-90 transform'"
            class="h-5 w-5 text-amber-900"
          />
        </DisclosureButton>

        <DisclosurePanel class="mt-8 bg-white shadow overflow-hidden rounded-md">
          <ul class="divide-y divide-amber-400">
            <li
              v-for="character in missing_characters_scopes"
              :key="character.name"
              class=" px-6 py-4 sm:flex sm:items-start sm:justify-between bg-amber-200"
            >
              <div class="sm:flex sm:items-start">
                <eve-image
                  :object="character"
                  :size="128"
                />
                <div class="mt-3 sm:mt-0 sm:ml-4">
                  <div class="text-sm leading-5 font-medium text-amber-900">
                    {{ character.name }}
                  </div>
                  <div class="mt-1 text-sm leading-5 text-amber-600 sm:flex sm:items-center">
                    {{ getMissingText(character.missing_scopes) }}
                  </div>
                </div>
              </div>
              <div class="mt-4 sm:mt-0 sm:ml-6 sm:shrink-0">
                <span class="inline-flex rounded-md shadow-sm">
                  <a
                    :href="route('auth.eve.step_up', { character_id: character.character_id, add_scopes: getMissingScopeString(character.missing_scopes)})"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150"
                  >
                    Fix
                  </a>
                </span>
              </div>
            </li>

            <!-- More items... -->
          </ul>
        </DisclosurePanel>
        <ul class="mt-5 rounded-md bg-amber-200 divide-y divide-black" />
      </div>
    </Disclosure>
  </div>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue"
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
} from '@headlessui/vue'
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import {useGlobalState} from "@/Functions/useGlobalState";


const props = defineProps({
  dispatchTransferObject: {
    type: Object,
    required: true
  }
})

const requiredScopes = _.get(props.dispatchTransferObject, 'required_scopes', [])
const characters = usePage().props.user.data.characters

const globalState = useGlobalState()
const state = globalState.openScopeWarning

const flipState = function () {
    globalState.flipScopeWarning()
}

const missing_characters_scopes = computed( function () {
    let returnValue = []

    _.forEach(characters, function (character) {

        let missing_scopes = _.difference(requiredScopes, character.scopes)

        if(_.isEmpty(missing_scopes))
            return

        returnValue.push({
            character_id: character.character_id,
            name: character.name,
            missing_scopes: missing_scopes
        })
    })

    return returnValue;
})
const getMissingText = function(missing_scopes) {

    return 'Missing the following scopes: ' + _.join(missing_scopes, ', ')
}

const getMissingScopeString = function (missing_scopes) {
    return _.toString(missing_scopes)
}

</script>
