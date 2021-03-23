<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>
    <PageHeader>
      {{ pageTitle }}
    </PageHeader>
    <WideLists>
      <template #header>
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Characters
          </h3>
        </div>
      </template>
      <template #elements>
        <WideListElement
          v-for="character in user.data.characters"
          :key="character.character_id"
          url="#"
        >
          <template #avatar>
            <span class="inline-block relative">
              <eve-image
                :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
                :object="character"
                :size="128"
              />
            </span>
          </template>
          <template #upper_left>
            <div class="inline-flex items-center">
              <div>{{ character.name }}</div>
              <span
                v-if="character.character_id === mainId"
                class="ml-2.5 px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-purple-100 text-purple-800"
              >
                Main Character
              </span>
            </div>
          </template>
          <template #navigation>
            <svg
              class="text-gray-400 h-5 w-5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </template>
        </WideListElement>
      </template>
      <template #footer>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <!-- Content goes here -->
          <div class="text-right">
            <span class="inline-flex rounded-md shadow-sm">
              <a
                :href="$route('auth.eve')"
                type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition ease-in-out duration-150"
              >
                <svg
                  class="-ml-0.5 mr-2 h-4 w-4"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                </svg>
                Add more
              </a>
            </span>
          </div>
          <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
        </div>
      </template>
    </WideLists>
  </div>
</template>

<script>
    import EveImage from "@/Shared/EveImage"
    import {Inertia} from "@inertiajs/inertia"
    import WideLists from "@/Shared/WideLists"
    import WideListElement from "@/Shared/WideListElement"
    import PageHeader from "../../Shared/Layout/PageHeader";
    export default {
        name: "UserSettings",
        components: {PageHeader, WideListElement, WideLists, EveImage},
        props: {
            user: {
                typer: Object,
                required: true
            }
        },
        data()  {
            return {
                mainId: this.user.data.main_character.character_id,
                pageTitle: 'User Settings'
            }
        },
        watch: {
            user: function () {
                this.mainId = this.user.data.main_character.character_id;
            }
        },
        methods: {
            isActive(characterId) {
                return characterId === this.mainId
            },
            updateMainCharacter(characterID) {

                if(characterID !== this.mainId)
                    return Inertia.post(route('update.main_character'),{ character_id: characterID});
            }
        }
    }
</script>

<style scoped>

</style>
