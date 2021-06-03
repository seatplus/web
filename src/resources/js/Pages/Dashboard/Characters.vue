<template>
  <div class="pb-5 border-b border-gray-200 space-y-2">
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      Characters
    </h3>
    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      Below you find all characters you have added to this seatplus instance
    </p>
    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <li
        v-for="character in characters"
        :key="character.character_id"
      >
        <LeftAligned>
          <template #header>
            <EntityBlock :entity="character" />
          </template>
          <LeftAlignedData v-if="character.balance">
            <template #title>
              Balance
            </template>
            <template #description>
              ISK {{ character.balance.toLocaleString() }}
            </template>
          </LeftAlignedData>
          <LeftAlignedData>
            <template #title>
              Created
            </template>
            <template #description>
              <Time :timestamp="character.birthday" />
            </template>
          </LeftAlignedData>
        </LeftAligned>
      </li>
      <li class="col-span-1 bg-white rounded-lg shadow flex flex-wrap content-center">
        <a
          :href="$route('auth.eve')"
          type="button"
          class="py-4 inline-flex items-center justify-center items-center w-full h-full border border-transparent shadow-sm text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <!-- Heroicon name: solid/user-add -->
          <svg
            class="-ml-1 mr-3 h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
          </svg>
          Add characters
        </a>
        <!--                <div class="w-full flex items-center justify-center p-6 space-x-6">
                            <svg class="max-h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <div>Test</div>
                        </div>-->
      </li>
    </ul>
  </div>
</template>

<script>
import CharacterApplication from "./CharacterApplication"
import EveImage from "@/Shared/EveImage"
import LeftAligned from "../../Shared/Layout/DataDisplay/LeftAligned";
import EntityBlock from "../../Shared/Layout/Eve/EntityBlock";
import LeftAlignedData from "../../Shared/Layout/DataDisplay/LeftAlignedData";
import Time from "../../Shared/Time";
export default {
    name: "Characters",
    components: {Time, LeftAlignedData, EntityBlock, LeftAligned, EveImage, CharacterApplication},
    props: {
        characters: {
            type: Array
        },
        enlistments: {
            type: Array,
            default: []
        }
    },
    computed: {
        hasOpenEnlistments() {
            return !_.isEmpty(this.enlistments)
        }
    }

}
</script>

<style scoped>

</style>
