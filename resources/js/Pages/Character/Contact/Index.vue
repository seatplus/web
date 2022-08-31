<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />

    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <EntitySelectionButton />
      </template>
    </PageHeader>

    <div class="space-y-4">
      <CharacterContactsComponent
        v-for="character in characters"
        :key="character.character_id"
        :character="character"
        :corporation_id="character.corporation_id"
        :alliance_id="character.alliance_id"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import CharacterContactsComponent from "@/Shared/Components/Contacts/CharacterContactsComponent.vue";
export default {
    name: "Index",
    components: {
        CharacterContactsComponent,
        RequiredScopesWarning,
        DispatchUpdateButton,
        EntitySelectionButton, PageHeader},
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object
        },
        characters: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            pageTitle: 'Character Contacts',
        }
    }
}
</script>

<style scoped>

</style>
