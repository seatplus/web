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
        v-for="character_affiliation in characters"
        :key="character_affiliation.character_id"
        :character="character_affiliation.character"
        :corporation_id="character_affiliation.corporation_id"
        :alliance_id="character_affiliation.alliance_id"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import CharacterContactsComponent from "@/Shared/Components/Contacts/CharacterContactsComponent";
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
