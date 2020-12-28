<template>
    <div v-if="moderators.length > 0">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Moderators
        </h3>
        <ul :class="[{'lg:grid-cols-3' : !twoColumns},'grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5']">
            <li :key="moderator.affiliatable_id" v-for="moderator in enhancedModerators" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ getMainName(moderator.affiliatable) }}</h3>
                            <!--<span :class="[{'text-teal-800 bg-teal-100': member.status === 'member'}, {'text-yellow-800 bg-yellow-100': member.status === 'paused'}, 'flex-shrink-0 inline-block px-2 py-0.5  text-xs leading-4 font-medium  rounded-full capitalize']"> {{ member.status }} </span>-->
                        </div>
                        <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ getCharacterNames(moderator.affiliatable) }}</p>
                    </div>
                    <EveImage :object="moderator.affiliatable.main_character" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
                </div>
                <div class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <div class="w-0 flex-1 flex border-r border-gray-200">
                            <button @click="removeMember(moderator)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-red-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-red-700 focus:outline-none focus:ring-blue focus:border-red-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"></path>
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
  import axios from "axios"
  export default {
      name: "Moderators",
      components: {EveImage},
      props: {
          value: {},
          twoColumns: {
              type: Boolean,
              requires: false,
              default: false
          }
      },
      data() {
          return {
              moderators: this.value
          }
      },
      methods: {
          getMainName(user) {
              return user.main_character.name ?? 'unknown'
          },
          getCharacterNames(user) {

              let characters = _.remove(user.characters, function (character) {
                  return character.character_id === user.main_character_id
              })
                  .map((character) => character.name)


              return _.shuffle(characters).join(', ')
          },
          removeMember(member) {

              this.moderators = _.remove(this.moderators, (moderator) => moderator.affiliatable.id !== member.affiliatable.id)
          },
      },
      computed: {
          enhancedModerators() {
              return _.map(this.moderators, (moderator) => {
                  if(moderator.hasOwnProperty('user'))
                      moderator.affiliatable = moderator.user

                  return moderator
              })
          }
      },
      watch: {
          moderators(value) {
              this.$emit('input', value)
          },
          value(value) {
              this.moderators = value
          }
      }
  }
</script>

<style scoped>

</style>
