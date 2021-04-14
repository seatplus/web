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
      <CharacterContactPanel
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
import Layout from "@/Shared/SidebarLayout/Layout";
import CharacterContactPanel from "@/Shared/Components/CharacterContactPanel";
import PageHeader from "@/Shared/Layout/PageHeader";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
export default {
  name: "Index",
  components: {
    RequiredScopesWarning,
    DispatchUpdateButton,
      EntitySelectionButton, PageHeader, CharacterContactPanel,},
  layout: (h, page) => h(Layout, { dispatch_transfer_object: page.props.dispatch_transfer_object }, [page]),
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
