<template>
  <div class="space-y-4">
    <PageHeader :page-title="pageTitle" :breadcrumbs="breadcrumbs" />
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <!--Header-->
      <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
        <!-- Content goes here -->
        <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
          <div class="ml-4 mt-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Manage Members {{ role.name }}
            </h3>
          </div>
        </div>
        <!-- We use less vertical padding on card headers on desktop than on body sections -->
      </div>

      <!--Content below-->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
          <InfiniteLoadingHelper
            v-slot="{results}"
            route-name="acl.members"
            :params="{role_id: role.id}"
          >
            <li
              v-for="member in sortMembers(results)"
              :key="member.id"
            >
              <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="inline-flex items-center space-x-3">
                        <EveImage
                          :object="member.main_character"
                          :size="256"
                          tailwind_class="h-8 w-8 rounded-full"
                        />
                        <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                          {{ member.main_character.name }}
                        </div>
                        <span :class="[{'bg-emerald-100 text-emerald-800': member.status === 'member', 'bg-blue-100 text-blue-800': member.status === 'waitlist', 'bg-amber-100 text-amber-800': member.status === 'paused'},'px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize']">
                          {{ member.status }}
                        </span>
                      </div>
                      <div
                        v-if="member.characters.length > 0"
                        class="truncate"
                      >
                        <AvatarGroupTopToBottom
                          :objects="member.characters"
                          :size="256"
                        />
                      </div>
                    </div>

                    <div class="ml-2 shrink-0 flex">
                      <span
                        v-if="member.status === 'waitlist'"
                        class="relative z-0 inline-flex shadow-sm rounded-md"
                      >
                        <button
                          type="button"
                          class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                          @click="approve(member)"
                        >
                          <svg
                            class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Approve
                        </button>
                        <button
                          type="button"
                          class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                          @click="removeMember(member)"
                        >
                          <svg
                            class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Deny
                        </button>
                      </span>
                      <span
                        v-if="member.status === 'member'"
                        class="inline-flex rounded-md shadow-sm"
                      >
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-gray-300t text-sm leading-4 font-medium rounded-md bg-white hover:text-gray-500 focus:outline-none focus:border-blue-700 focus:ring-blue active:text-gray-700 active:bg-gray-100 transition ease-in-out duration-150"
                          @click="removeMember(member)"
                        >
                          <svg
                            class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Kick member
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </InfiniteLoadingHelper>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
    import EveImage from "@/Shared/EveImage.vue"
    import AvatarGroupTopToBottom from "@/Shared/AvatarGroupTopToBottom.vue"
    import PageHeader from "@/Shared/Layout/PageHeader.vue";
    import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";
    import {router} from "@inertiajs/vue3";
    export default {
        name: "ModerateMembers",
        components: {
            InfiniteLoadingHelper,
            PageHeader,
            AvatarGroupTopToBottom, EveImage
        },
        props: {
            role: {
                required: true,
                type: Object
            }
        },
        data()  {
            return {
                pageTitle: 'Manage Members',
                breadcrumbs: [
                    {
                        name: 'Control Group',
                        route: route('acl.groups')
                    }
                ]
            }
        },
        methods: {
            approve(member) {

                let data = {
                    user_id: member.id,
                    role_id: this.role.id
                };

                router.post(route('acl.join'), data, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
            removeMember(member) {

                router.delete(route('acl.leave', { role_id: this.role.id, user_id: member.id}), {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
            sortMembers(members) {

                let cleanMembers = _.map(members, function (member) {
                    member.characters = _.reject(member.characters, (character) => character.character_id === member.main_character.character_id)
                    return member
                })

                let sortedMembers = []

                _.each(['waitlist', 'paused', 'member'], function (type) {

                    let filteredMembers = _.filter(cleanMembers, (member) => member.status === type)

                    _.each(filteredMembers, (filteredMember) => sortedMembers.push(filteredMember))
                })

                return sortedMembers

            }
        }
    }
</script>

<style scoped>

</style>
