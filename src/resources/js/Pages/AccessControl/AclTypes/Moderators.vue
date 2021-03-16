<template>
  <div v-if="moderators.length > 0">
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      Moderators
    </h3>
    <ul :class="[{'lg:grid-cols-3' : !twoColumns},'grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5']">
      <li
        v-for="moderator in moderators"
        :key="moderator.id"
        class="col-span-1 bg-white rounded-lg shadow"
      >
        <div class="w-full flex items-center justify-between p-6 space-x-6">
          <EntityBlock
            :entity="moderator.main_character"
            :image-size="10"
            name-font-size="sm"
          />
        </div>
        <div class="border-t border-gray-200">
          <div class="-mt-px flex">
            <div class="w-0 flex-1 flex border-r border-gray-200">
              <button
                class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-red-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-red-700 focus:outline-none focus:ring-blue focus:border-red-300 focus:z-10 transition ease-in-out duration-150"
                @click="removeMember(moderator)"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z" />
                </svg>
                <span class="ml-3">
                  Remove moderator
                </span>
              </button>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage"
import EntityBlock from "../../../Shared/Layout/Eve/EntityBlock";
export default {
    name: "Moderators",
    components: {EntityBlock},
    props: {
        modelValue: {},
        twoColumns: {
            type: Boolean,
            requires: false,
            default: false
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            moderators: this.modelValue
        }
    },
    watch: {
        moderators(value) {
            this.$emit('update:modelValue', value)
        },
        modelValue(value) {
            this.moderators = value
        }
    },
    methods: {
        getMainName(user) {
            return user.main_character.name != null ? user.main_character.name : 'unknown'
        },
        getCharacterNames(user) {

            let characters = _.remove(user.characters, function (character) {
                return character.character_id === user.main_character_id
            })
                .map((character) => character.name)


            return _.shuffle(characters).join(', ')
        },
        removeMember(member) {

            this.moderators = _.remove(this.moderators, (moderator) => moderator.id !== member.id)
        },
    }
}
</script>

<style scoped>

</style>
