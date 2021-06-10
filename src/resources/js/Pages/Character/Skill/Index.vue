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

    <SkillsComponent
      v-for="character_id in character_ids"
      :key="character_id"
      :character-id="character_id"
    />
  </div>
</template>

<script>
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import PageHeader from "@/Shared/Layout/PageHeader";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton";
import SkillsComponent from "../../../Shared/Components/Skills/SkillsComponent";
export default {
    name: "Index",
    components: {SkillsComponent, EntitySelectionButton, DispatchUpdateButton, PageHeader, RequiredScopesWarning},
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object
        },
        character_ids: {
            required: true,
            type: Array
        }
    },
    setup() {

        return {
            pageTitle: 'Character Skills'
        }
    }
}
</script>

<style scoped>

</style>