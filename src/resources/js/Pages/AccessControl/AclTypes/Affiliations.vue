<template>
    <div v-if="entities.length > 0">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Affiliations
        </h3>
        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-6 sm:mt-5">
            <li :key="entity.affiliatable_id" v-for="entity in this.entities" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ getEntityName(entity) }}</h3>
                            <span class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Affiliated</span>
                        </div>
                        <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ getEntityType(entity) }}</p>
                    </div>
                    <EveImage :object="getObject(entity)" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
                </div>
                <div v-if="requiresRemovalButton" class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <div class="w-0 flex-1 flex border-r border-gray-200">
                            <button @click="removeMember(entity.affiliatable)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-red-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-red-700 focus:outline-none focus:shadow-outline-blue focus:border-red-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"></path>
                                </svg>
                                <span class="ml-3">Remove member</span>
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
      name: "Affiliations",
      components: {EveImage},
      props: {
          value: {}
      },
      data() {
          return {
              entities: this.value
          }
      },
      methods: {
          getEntityName(entity) {
              if(entity.hasOwnProperty('affiliatable'))
                  return entity.affiliatable.name

              return entity.name
          },
          getEntityType(entity) {

              if(entity.hasOwnProperty('category'))
                  return _.capitalize(entity.category)

              if(entity.affiliatable.hasOwnProperty('corporation_id'))
                  return 'Corporation'

              return 'Alliance'

          },
          requiresRemovalButton() {
              return ['manual', 'hidden'].indexOf(this.role.type) >= 0
          },
          removeMember(user) {

              this.members = _.remove(this.members, (member) => member.user_id !== user.id )
          },
          getObject(entity) {

              return entity.hasOwnProperty('affiliatable') ? entity.affiliatable : entity
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
