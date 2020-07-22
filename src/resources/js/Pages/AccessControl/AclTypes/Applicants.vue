<template>
    <div v-if="filteredMembers.length > 0">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Applicants
        </h3>
        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5">
            <li :key="member.user.id" v-for="member in filteredMembers" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ getMainName(member.user) }}</h3>
                            <span class="text-blue-800 bg-blue-100 flex-shrink-0 inline-block px-2 py-0.5 text-xs leading-4 font-medium rounded-full capitalize"> {{ member.status }} </span>
                        </div>
                        <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ getCharacterNames(member.user) }}</p>
                    </div>
                    <EveImage :object="member.user.main_character" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
                </div>
                <div class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <div class="w-0 flex-1 flex border-r border-gray-200">
                            <button @click="addMember(member)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-green-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-green-700 focus:outline-none focus:shadow-outline-green focus:border-green-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                </svg>
                                <span class="ml-3">Approve</span>
                            </button>
                        </div>
                        <div class="-ml-px w-0 flex-1 flex">
                            <button @click="removeMember(member)" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:bg-red-100 hover:text-red-700 focus:outline-none focus:shadow-outline-red focus:border-red-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"></path>
                                </svg>
                                <span class="ml-3">Deny</span>
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


  export default {
      name: "Applicants",
      components: {EveImage},
      props: {
          value: {},
      },
      data() {
          return {
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
          removeMember(member) {

              this.$inertia.delete(this.$route('acl.leave', [member.role_id, member.user_id]), {
                  replace: false,
                  preserveState: false,
                  preserveScroll: false,
                  only: [],
              })
          },
          addMember(member) {

              let data = {
                  user_id: member.user_id,
                  role_id: member.role_id
              };

              this.$inertia.post(this.$route('acl.join'), data, {
                  replace: false,
                  preserveState: false,
                  preserveScroll: false,
                  only: [],
              })
          }
      },
      computed: {
          filteredMembers() {
              return _.filter(this.members, (member) => member.hasOwnProperty('status') ? member.status === 'waitlist' : true)
          }
      },
      watch: {
          members(value) {
              this.$emit('input', value)
          },
          value(value) {
              this.members = value
          }
      }
  }
</script>

<style scoped>

</style>
