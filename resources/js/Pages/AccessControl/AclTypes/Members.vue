<template>
  <div v-if="members.length > 0">
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      <slot name="title">
        Members
      </slot>
    </h3>
    <ul :class="[{'lg:grid-cols-3' : !twoColumns},'grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6 sm:mt-5']">
      <li
        v-for="member in filteredMembers"
        :key="member.id"
        class="col-span-1 bg-white rounded-lg shadow"
      >
        <div class="w-full flex items-center justify-between p-6 space-x-6">
          <div class="flex-1 truncate">
            <div class="flex items-center space-x-3">
              <h3 class="text-gray-900 text-sm leading-5 font-medium truncate">
                {{ getMainName(member) }}
              </h3>
              <span :class="[{'text-teal-800 bg-teal-100': member.status === 'member'}, {'text-yellow-800 bg-yellow-100': member.status === 'paused'}, 'flex-shrink-0 inline-block px-2 py-0.5  text-xs leading-4 font-medium  rounded-full capitalize']"> {{ member.status }} </span>
            </div>
            <p class="mt-1 text-gray-500 text-sm leading-5 truncate">
              {{ getCharacterNames(member) }}
            </p>
          </div>
          <EveImage
            :object="getMain(member)"
            :size="256"
            tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0"
          />
        </div>
        <div
          v-if="requiresRemovalButton"
          class="border-t border-gray-200"
        >
          <div class="-mt-px flex">
            <div class="w-0 flex-1 flex border-r border-gray-200">
              <button
                class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 hover:bg-red-100 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-red-700 focus:outline-none focus:ring-blue focus:border-red-300 focus:z-10 transition ease-in-out duration-150"
                @click="removeMember(member)"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z" />
                </svg>
                <span class="ml-3">
                  <slot>
                    Remove member
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
import EveImage from "../../../Shared/EveImage"
export default {
    name: "Members",
    components: {EveImage},
    props: {
        modelValue: {},
        requiresRemovalButton: {
            type: Boolean,
            requires: false,
            default: false
        },
        twoColumns: {
            type: Boolean,
            requires: false,
            default: false
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            members: this.modelValue
        }
    },
    computed: {
        filteredMembers() {
            return _.filter(this.members, (member) => member.hasOwnProperty('status') ? member.status !== 'waitlist' : true)
        }
    },
    watch: {
        members(value) {
            this.$emit('update:modelValue', value)
        },
        value(value) {
            this.members = value
        }
    },
    methods: {
        getMainName(member) {

            return _.get(this.getMain(member), 'name')
        },
        getMain(member) {
            return member.user ? _.get(member, 'user.main_character') : _.get(member, 'main_character')
        },
        getCharacterNames(member) {

            let characters = _.map(_.get(member, 'user.characters', []), character => character.name)

            return _.shuffle(characters).join(', ')
        },
        removeMember(member) {

            this.members = _.remove(this.members, (existing_member) => existing_member.user_id !== member.user.id)
        },
    }
}
</script>

<style scoped>

</style>
