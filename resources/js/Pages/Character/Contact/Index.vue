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

    <Deferred data="contacts">
      <template #fallback>
        <div class="space-y-4">
          <div
            v-for="character in characters"
            :key="character.character_id"
            class="rounded-lg border border-gray-300 bg-white p-4 shadow-xs space-y-4"
          >
            <div class="flex items-center space-x-3">
              <div class="h-10 w-10 rounded-full bg-gray-200 animate-pulse" />
              <div class="flex-1 space-y-2">
                <div class="h-3 w-1/3 rounded bg-gray-200 animate-pulse" />
                <div class="h-3 w-1/4 rounded bg-gray-100 animate-pulse" />
              </div>
            </div>
            <div class="space-y-2">
              <div
                v-for="n in 3"
                :key="n"
                class="h-3 w-full rounded bg-gray-100 animate-pulse"
              />
            </div>
          </div>
        </div>
      </template>

      <div class="space-y-4">
        <CharacterContactsComponent
          v-for="character in characters"
          :key="character.character_id"
          :character="character"
          :contacts="contacts[character.character_id] ?? []"
        />
      </div>
    </Deferred>
  </div>
</template>

<script setup>
import { Deferred } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import CharacterContactsComponent from "@/Shared/Components/Contacts/CharacterContactsComponent.vue";

defineProps({
    dispatchTransferObject: {
        required: true,
        type: Object
    },
    characters: {
        required: true,
        type: Array
    },
    contacts: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = 'Character Contacts'
</script>

<style scoped>

</style>
