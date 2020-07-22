<template>
    <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <slot name="title">
                Available Users
            </slot>
        </h3>
        <ul :class="[{'lg:grid-cols-3' : !twoColumns},'grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5']">
            <li :key="user.id" v-for="user of this.filteredUsers" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ getMainName(user) }}</h3>
                            <!--<span class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Member</span>-->
                        </div>
                        <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ getCharacterNames(user) }}</p>
                    </div>
                    <EveImage :object="user.main_character" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
                </div>
                <div v-if="requiresAddingButton" class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <div class="w-0 flex-1 flex border-r border-gray-200">
                            <button @click="addMember(user)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-green-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-green-700 focus:outline-none focus:shadow-outline-blue focus:border-green-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                </svg>
                                <span class="ml-3">
                                    <slot name="button-text">
                                        Add member
                                    </slot>
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
    name: "Users",
      components: {EveImage},
      props: {
          value: {},
          requiresAddingButton: {
              required: false,
              type: Boolean,
              default: false
          },
          twoColumns: {
              type: Boolean,
              requires: false,
              default: false
          }
      },
      data() {
          return {
              users: [],
              members: this.value
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
          async getInfo() {
              return await axios.get(this.$route('list.users'))
                  .then((response) => {

                      response.data.data.forEach(object => this.users.push(object))

                      if (_.isNull(response.data.next_page_url))
                          return

                      return this.getInfo(response.data.next_page_url)
                  })
                  .catch(error => console.log(error))
          },
          addMember(user) {

              let memberObject = {
                  user_id: user.id,
                  user: user
              }

              this.members = [... this.members , memberObject]
          },
      },
      computed: {
          filteredUsers() {

              let memberIds = []

              _.each(this.value, (member) => {

                  if(member.hasOwnProperty('user'))
                      memberIds.push(member.user.id)

                  if(member.hasOwnProperty('affiliatable'))
                      memberIds.push(member.affiliatable.id)
              })

              return _.filter(this.users, (user) => memberIds.indexOf(user.id) === -1)
          }
      },
      watch: {
          members(value) {
              this.$emit('input', value)
          },
          value(data) {
              this.members = data
          }
      },
      mounted() {
          this.getInfo()
      }
  }
</script>

<style scoped>

</style>
