<template>
  <div class="space-y-3">

    <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />

    <PageHeader :page-title="pageTitle">
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <EntitySelectionButton />
      </template>
    </PageHeader>

    <Deferred :data="['skills', 'skillQueue']">
      <template #fallback>
        <div class="space-y-4">
          <div
            v-for="character_id in character_ids"
            :key="character_id"
            class="space-y-4"
          >
            <div class="border-b border-gray-200 pb-4">
              <div class="h-6 w-1/3 rounded bg-gray-200 animate-pulse" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200">
                <div class="px-4 py-5 sm:px-6">
                  <div class="h-4 w-1/2 rounded bg-gray-200 animate-pulse" />
                </div>
                <div class="px-4 py-5 sm:px-6 space-y-3">
                  <div
                    v-for="row in 3"
                    :key="row"
                    class="h-3 w-2/3 rounded bg-gray-100 animate-pulse"
                  />
                </div>
              </div>
              <div class="col-span-2 space-y-4">
                <div
                  v-for="n in 2"
                  :key="n"
                  class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200"
                >
                  <div class="px-4 py-5 sm:px-6">
                    <div class="h-4 w-1/3 rounded bg-gray-200 animate-pulse" />
                  </div>
                  <div class="px-4 py-5 sm:px-6 space-y-3">
                    <div
                      v-for="row in 3"
                      :key="row"
                      class="h-3 w-2/3 rounded bg-gray-100 animate-pulse"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <SkillsComponent
        v-for="character_id in character_ids"
        :key="character_id"
        :character-id="character_id"
        :skills="skills[character_id]"
        :skill-queue="skillQueue[character_id]"
      />
    </Deferred>
  </div>
</template>

<script setup>
import { Deferred } from "@inertiajs/vue3";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import SkillsComponent from "@/Shared/Components/Skills/SkillsComponent.vue";

defineProps({
    dispatchTransferObject: {
        required: true,
        type: Object
    },
    character_ids: {
        required: true,
        type: Array
    },
    skills: {
        type: Object,
        default: () => ({})
    },
    skillQueue: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = 'Character Skills'
</script>

<style scoped>

</style>
