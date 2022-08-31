<template>
  <div class="flex-1 flex flex-col p-8">
    <Avatar :name="role.name" />
    <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">
      {{ role.name }}
    </h3>
    <dl class="mt-1 flex-grow flex flex-col justify-between">
      <dd class="text-gray-500 text-sm leading-5">
        {{ role.members }} {{ role.members > 1 ? 'Members' : 'Member' }}
      </dd>
      <dd class="mt-3">
        <span :class="[isPausedOrWaitlist? 'bg-yellow-100 text-yellow-800': 'bg-teal-100 text-teal-800','px-2 py-1  text-xs leading-4 font-medium  rounded-full']">{{ role.status ? role.status : role.type }}</span>
      </dd>
    </dl>
  </div>
  <div
    v-if="numberOfButtons>0" 
    class="border-t border-gray-200"
  >
    <div
      class="grid gap-0 divide-x divide-gray-200"
      :class="{'grid-cols-1' : numberOfButtons === 1, 'grid-cols-2' : numberOfButtons === 2,'grid-cols-3' : numberOfButtons === 3, 'grid-cols-4' : numberOfButtons === 4}"
    >
      <Link
        v-if="isJoinable"
        as="button"
        :href="route('acl.join')"
        method="post"
        :data="{ role_id: role.id }"
        class="w-full flex justify-center py-4 px-4 text-sm leading-5 text-gray-700 font-medium"
      >
        <svg
          class="-ml-1 mr-2 w-5 h-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
        </svg>
        Join
      </Link>
      <Link
        v-if="isLeavable"
        as="button"
        :href="route('acl.leave', leaveParams)"
        method="delete"
        class="w-full flex justify-center py-4 px-4 text-sm leading-5 text-gray-700 font-medium"
      >
        <svg
          class="-ml-1 mr-2 w-5 h-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z" />
        </svg>
        Leave
      </Link>
      <Link
        v-if="isModeratable"
        as="button"
        :href="route('manage.acl.members', role.id)"
        class="w-full flex justify-center py-4 px-4 text-sm leading-5 text-gray-700 font-medium"
      >
        <!-- Heroicon name: solid/mail -->
        <svg
          class="-ml-1 mr-2 w-5 h-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
        </svg>
        Moderate
      </Link>
      <Link
        v-if="isEditable"
        as="button"
        :href="route('acl.edit', role.id)"
        class="w-full flex justify-center py-4 px-4 text-sm leading-5 text-gray-700 font-medium"
      >
        <svg
          class="-ml-1 mr-2 w-5 h-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
          <path
            fill-rule="evenodd"
            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
            clip-rule="evenodd"
          />
        </svg>
        Edit
      </Link>
      <Link
        v-if="isManageable"
        as="button"
        :href="route('acl.manage', role.id)"
        class="w-full flex justify-center py-4 px-4 text-sm leading-5 text-gray-700 font-medium"
      >
        <svg
          class="-ml-1 mr-2 w-5 h-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
        </svg>
        Manage
      </Link>
    </div> 
  </div>
</template>

<script>
import Avatar from "@/Shared/Avatar.vue";
import { Link } from '@inertiajs/inertia-vue3'
export default {
    name: "ControlGroup",
    components: {Avatar, Link},
    props: {
        role: {
            required: true,
            type: Object
        },
    },
    computed: {
        isJoinable() {
            return (['on-request', 'opt-in'].indexOf(this.role.type) > -1) && !this.isLeavable
        },
        isLeavable() {
            return !!this.role.status
        },
        isModeratable() {
            return this.role.can_moderate && !this.role.can_edit
        },
        isPausedOrWaitlist() {
            return ['paused', 'waitlist'].indexOf(this.role.status) > -1
        },
        isEditable() {
            return this.role.can_edit
        },
        isManageable() {
            return this.role.can_edit
        },
        numberOfButtons() {
            let number = 0

            if(this.isJoinable || this.isLeavable)
                number++

            if(this.isEditable)
                number += 2

            if(this.isModeratable)
                number++

            return number
        },
        leaveParams() {
            let user_id = this.$page.props.user.data.id;

            return {role_id: this.role.id, user_id: user_id}
        }
    }
}
</script>

<style scoped>

</style>