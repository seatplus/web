<template>
  <div class="space-y-3">
    <PageHeader
      :page-title="role.name"
      :breadcrumbs="breadcrumbs"
    >
      <template #primary>
        <Link
          v-if="can_edit"
          :href="manage(role.id).url"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit
        </Link>
      </template>
    </PageHeader>

    <!-- Role overview card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6 flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">
            {{ role.name }}
          </h2>
          <p class="mt-1 text-sm text-gray-500 capitalize">
            {{ typeLabel }}
          </p>
        </div>
        <span :class="typeBadgeClass">{{ role.type }}</span>
      </div>

      <div
        v-if="role.permissions.length > 0"
        class="border-t border-gray-200 px-4 py-5 sm:p-6"
      >
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">
          Permissions granted
        </h3>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="permission in role.permissions"
            :key="permission"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
          >
            {{ permission }}
          </span>
        </div>
      </div>
    </div>

    <!-- Affiliations -->
    <div
      v-if="role.affiliations.length > 0"
      class="bg-white overflow-hidden shadow rounded-lg"
    >
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
          Affiliations
        </h3>
        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li
            v-for="affiliation in role.affiliations"
            :key="affiliation.id"
            class="col-span-1 bg-gray-50 rounded-lg p-4 flex items-center space-x-3"
          >
            <EntityByIdBlock
              :id="affiliation.id"
              :image-size="10"
            />
            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 capitalize shrink-0">
              {{ affiliation.affiliation_type }}
            </span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Moderators (on-request only) -->
    <div
      v-if="role.moderators.length > 0"
      class="bg-white overflow-hidden shadow rounded-lg"
    >
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
          Moderators
        </h3>
        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li
            v-for="moderator in role.moderators"
            :key="moderator.id"
            class="col-span-1 bg-white rounded-lg shadow"
          >
            <div class="w-full flex items-center justify-between p-4 space-x-3">
              <EntityBlock
                v-if="moderator.main_character"
                :entity="moderator.main_character"
                :image-size="10"
                name-font-size="sm"
              />
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Members -->
    <div
      v-if="role.members.length > 0"
      class="bg-white overflow-hidden shadow rounded-lg"
    >
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
          Members
          <span class="ml-2 text-sm font-normal text-gray-500">({{ role.members.length }})</span>
        </h3>
        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li
            v-for="member in role.members"
            :key="member.id"
            class="col-span-1 bg-white rounded-lg shadow"
          >
            <div class="w-full flex items-center justify-between p-4 space-x-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2">
                  <EveImage
                    v-if="member.user && member.user.main_character"
                    :object="member.user.main_character"
                    :size="256"
                    tailwind_class="w-10 h-10 bg-gray-300 rounded-full shrink-0"
                  />
                  <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ member.user && member.user.main_character ? member.user.main_character.name : 'Unknown' }}
                    </p>
                    <div class="flex items-center space-x-1 mt-0.5">
                      <span :class="statusBadgeClass(member.status)">{{ member.status }}</span>
                      <span
                        v-if="member.can_moderate"
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                      >
                        Moderator
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import PageHeader from '@/Shared/Layout/PageHeader.vue'
import EntityByIdBlock from '@/Shared/Layout/Eve/EntityByIdBlock.vue'
import EntityBlock from '@/Shared/Layout/Eve/EntityBlock.vue'
import EveImage from '@/Shared/EveImage.vue'
import { manage } from '@/routes/acl'
import { groups } from '@/routes/acl'

const props = defineProps({
    role: { type: Object, required: true },
    can_edit: { type: Boolean, default: false },
    activeSidebarElement: { type: String, default: '' },
})

const breadcrumbs = [
    { name: 'Access Control', route: groups().url },
]

const typeDescriptions = {
    automatic: 'Users with characters in the configured corporations or alliances are added automatically.',
    manual: 'Users are added manually by an administrator.',
    'on-request': 'Users can apply and a moderator approves or denies the request.',
    'opt-in': 'Users who meet the affiliation criteria can freely join or leave.',
}

const typeLabel = computed(() => typeDescriptions[props.role.type] ?? props.role.type)

const typeBadgeClasses = {
    automatic: 'bg-teal-100 text-teal-800',
    manual: 'bg-gray-100 text-gray-800',
    'on-request': 'bg-blue-100 text-blue-800',
    'opt-in': 'bg-indigo-100 text-indigo-800',
}

const typeBadgeClass = computed(() =>
    `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize ${typeBadgeClasses[props.role.type] ?? 'bg-gray-100 text-gray-800'}`
)

const statusBadgeClass = (status) => {
    const classes = {
        active: 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800',
        inactive: 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800',
        pending: 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    }
    return classes[status] ?? 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
}
</script>
