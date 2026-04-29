<template>
  <div class="space-y-6">
    <p class="text-sm text-gray-500">
      Members are managed manually — they must be individually added or removed. This role does not auto-assign based on corporation or alliance membership.
    </p>

    <div v-if="canEdit">
      <label class="block text-sm font-medium text-gray-700 mb-1">Role name</label>
      <input
        v-model="form.name"
        type="text"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
      />
    </div>

    <div>
      <h3 class="text-sm font-medium text-gray-700 mb-2">Permission scope — entities role holders can access data for</h3>

      <div v-if="canEdit" class="mb-4">
        <EsiAutosuggest
          label="Add corporation or alliance to scope"
          :categories="['corporation', 'alliance']"
          placeholder="Search by name…"
          :reset-after-select="true"
          @selected-object="addScope"
        />
      </div>

      <ul v-if="form.affiliated.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <li
          v-for="(scope, index) in form.affiliated"
          :key="`${scope.entity_id}-${scope.entity_type}`"
          class="col-span-1 bg-white rounded-lg shadow border border-gray-200"
        >
          <div class="w-full flex items-center justify-between p-4 space-x-4">
            <EntityByIdBlock :id="scope.entity_id" :image-size="8" />
            <span class="text-xs text-gray-400 capitalize">{{ scope.affiliation_type }}</span>
          </div>
          <div v-if="canEdit" class="border-t border-gray-200">
            <button
              class="w-full py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-lg transition"
              @click="removeScope(index)"
            >
              Remove
            </button>
          </div>
        </li>
      </ul>
      <p v-else class="text-sm text-gray-400 italic">No permission scope configured.</p>
    </div>

    <div v-if="canEdit" class="flex justify-end">
      <button
        :disabled="form.processing"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none disabled:opacity-50"
        @click="save"
      >
        Save
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import EsiAutosuggest from '@/Shared/Components/EsiAutosuggest.vue'
import EntityByIdBlock from '@/Shared/Layout/Eve/EntityByIdBlock.vue'

interface Affiliation {
  id: number
  entity_type: string
  affiliation_type: string
}

interface Role {
  id: number
  name: string
  type: string
  affiliations: Affiliation[]
}

const props = defineProps<{
  role: Role
  canEdit: boolean
}>()

const form = useForm({
  role_id: props.role.id,
  name: props.role.name,
  affiliated: props.role.affiliations.map((affiliation) => ({
    entity_id: affiliation.id,
    entity_type: affiliation.entity_type,
    affiliation_type: affiliation.affiliation_type,
  })),
})

function addScope(selectedOption: { id: number; category: string }): void {
  if (!selectedOption) return
  const alreadyAdded = form.affiliated.some(
    (scope) => scope.entity_id === selectedOption.id && scope.entity_type === selectedOption.category
  )
  if (!alreadyAdded) {
    form.affiliated.push({
      entity_id: selectedOption.id,
      entity_type: selectedOption.category,
      affiliation_type: 'allowed',
    })
  }
}

function removeScope(index: number): void {
  form.affiliated.splice(index, 1)
}

function save(): void {
  form.post(route('acl.update.manual', props.role.id))
}
</script>
