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

    <SkillsComponent
      v-for="character_id in character_ids"
      :key="character_id"
      :character-id="character_id"
    />
  </div>
</template>

<script>
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import SkillsComponent from "@/Shared/Components/Skills/SkillsComponent.vue";
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