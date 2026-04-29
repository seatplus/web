<template>
  <div class="space-y-3">
    <PageHeader :page-title="role.name" :breadcrumbs="breadcrumbs" />

    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center gap-3 mb-6">
          <span class="text-sm font-medium text-gray-500">Type:</span>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize">
            {{ role.type }}
          </span>
        </div>

        <component
          :is="typeComponent"
          :role="role"
          :can-edit="canEdit"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import PageHeader from '@/Shared/Layout/PageHeader.vue'
import AutomaticDetail from './Types/AutomaticDetail.vue'
import ManualDetail from './Types/ManualDetail.vue'
import OnRequestDetail from './Types/OnRequestDetail.vue'
import OptInDetail from './Types/OptInDetail.vue'

interface Affiliation {
  id: number
  entity_type: 'corporation' | 'alliance' | 'character'
  affiliation_type: 'allowed' | 'inverse' | 'forbidden'
}

interface Role {
  id: number
  name: string
  type: 'automatic' | 'manual' | 'on-request' | 'opt-in'
  affiliations: Affiliation[]
  permissions: string[]
}

const props = defineProps<{
  role: Role
  canEdit: boolean
  activeSidebarElement: string
}>()

const breadcrumbs = [{ name: 'Access Control', route: route('acl.groups') }]

const typeComponent = computed(() => {
  const componentMap = {
    'automatic': AutomaticDetail,
    'manual': ManualDetail,
    'on-request': OnRequestDetail,
    'opt-in': OptInDetail,
  } as const

  return componentMap[props.role.type as keyof typeof componentMap] ?? ManualDetail
})
</script>

