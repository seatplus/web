<template>
  <div>
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      <slot name="title">
        Available Users
      </slot>
    </h3>
    <label
      for="search_field"
      class="sr-only"
    > Search </label>
    <div class="relative w-full text-gray-400 focus-within:text-gray-600">
      <div class="ml-2 absolute inset-y-0 left-0 flex items-center pointer-events-none">
        <svg
          class="h-5 w-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        ><path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
        />
        </svg>
      </div>
      <input
        id="search_field"
        v-model="search"
        class="block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 border-gray-300 focus:outline-none focus:placeholder-gray-400 sm:text-sm"
        placeholder="Search"
        type="search"
      >
    </div>

    <InfiniteLoadingHelper
      :key="index"
      v-slot="{results}"
      route="list.users"
      :params="{name: search}"
    >
      <ul :class="[{'lg:grid-cols-3' : !twoColumns},'grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5']">
        <li
          v-for="user of filterUsers(results)"
          :key="user.id"
          class="col-span-1 bg-white rounded-lg shadow"
        >
          <div class="w-full flex items-center justify-between p-6 space-x-6">
            <div class="flex-1 truncate">
              <div class="flex items-center space-x-3">
                <h3 class="text-gray-900 text-sm leading-5 font-medium truncate">
                  {{ getMainName(user) }}
                </h3>
                <!--<span class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Member</span>-->
              </div>
              <p class="mt-1 text-gray-500 text-sm leading-5 truncate">
                {{ getCharacterNames(user) }}
              </p>
            </div>
            <EveImage
              :object="user.main_character"
              :size="256"
              tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0"
            />
          </div>

          <div
            v-if="requiresAddingButton"
            class="border-t border-gray-200"
          >
            <div class="-mt-px flex">
              <div class="w-0 flex-1 flex border-r border-gray-200">
                <button
                  class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-green-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-green-700 focus:outline-none focus:ring-blue focus:border-green-300 focus:z-10 transition ease-in-out duration-150"
                  @click="addMember(user)"
                >
                  <svg
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
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
    </InfiniteLoadingHelper>
  </div>
</template>

<script>
import EveImage from "../../../Shared/EveImage"
import InfiniteLoadingHelper from "../../../Shared/InfiniteLoadingHelper";

export default {
    name: "Users",
    components: {InfiniteLoadingHelper, EveImage},
    props: {
        modelValue: {},
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
            members: this.modelValue,
            search: '',
            index: +new Date()
        }
    },
    computed: {
        memberIds() {
            return _.map(this.members, (user) => user.id)
        }
    },
    watch: {
        members(value) {
            this.$emit('update:modelValue', value)
        },
        modelValue(data) {
            this.members = data
        },
        search() {

            const updateSearch = _.debounce(() => this.index++, 250)

            updateSearch()
        },
    },
    mounted() {
        //this.getInfo()
    },
    methods: {
        getMainName(user) {
            return user.main_character.name != null ? user.main_character.name : 'unknown'
        },
        getCharacterNames(user) {

            let characters = _.map(user.characters, character => character.name)

            return _.shuffle(characters).join(', ')
        },
        addMember(user) {

            this.members.push(user)
        },
        filterUsers(users) {
            return _.filter(users, (user) => !_.includes(this.memberIds, user.id))
        }
    }
}
</script>

<style scoped>

</style>
