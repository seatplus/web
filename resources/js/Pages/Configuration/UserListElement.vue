<template>
  <li>
    <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
      <div class="flex items-center px-4 py-4 sm:px-6">
        <div class="min-w-0 flex-1 flex items-center">
          <div class="flex overflow-x-visible">
            <EntityBlock :entity="user.main_character" />
          </div>
          <div class="min-w-0 flex-1 px-4 hidden md:grid md:grid-cols-2 md:gap-4">
            <EntityBlock
              v-for="character in characters"
              :key="character.character_id"
              :entity="character"
            />
          </div>
        </div>
        <div>
          <Link
            :href="route('impersonate.start', user.id)"
            class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
          >
            Impersonate
          </Link>
        </div>
      </div>
      <div
        v-if="characters.length > 0"
        class="px-4 py-4 sm:px-6 truncate text-gray-500 leading-5 text-sm"
      >
        Characters: {{ characterNames }}
      </div>
    </div>
  </li>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { Link } from '@inertiajs/inertia-vue3'
export default {
    name: "UserListElement",
    components: {EntityBlock, Link},
    props: {
        user: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    computed: {
        characters() {
            return _.reject(this.user.characters, (character)  => _.isEqual(character.character_id, this.user.main_character.character_id))
        },
        characterNames() {
            return _.join(_.map(this.characters, (character) => character.name), ', ')
        }
    }
}
</script>

<style scoped>

</style>
