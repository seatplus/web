<template>
  <li v-for="user of filteredResults">
    <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
      <div class="flex items-center px-4 py-4 sm:px-6">
        <div class="min-w-0 flex-1 flex items-center">
          <div class="flex-shrink-0 inline-flex items-center space-x-2">
            <EveImage
              tailwind_class="h-12 w-12 rounded-full"
              :size="256"
              :object="user.main_character"
            />
            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
              {{ user.main_character.name }}
            </div>
            <!--<img class="h-12 w-12 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">-->
          </div>
          <div class="min-w-0 flex-1 px-4">
            <!--md:grid md:grid-cols-2 md:gap-4-->

            <ul class="divide-y divide-gray-200">
              <CharacterComplianceElement
                v-for="character of user.characters"
                :key="character.character.character_id"
                :character="character"
              />
            </ul>
          </div>
        </div>
      </div>
    </div>
  </li>
  <div ref="scrollComponent"></div>
</template>

<script>
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
import EveImage from "@/Shared/EveImage";
import CharacterComplianceElement from "./CharacterComplianceElement";

export default {
  name: "UserDetails",
  components: {CharacterComplianceElement, EveImage},
  props: {
      parameters: {
          required: true,
          type: Object
      }
  },
  setup(props) {

      return useInfinityScrolling('user.compliance', props.parameters)

  },
  computed: {
    filteredResults() {
      return _.filter(this.result, (res) => !_.isEmpty(res) )
    }
  }
}
</script>

<style scoped>

</style>
