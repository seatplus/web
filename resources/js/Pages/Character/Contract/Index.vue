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

    <div class="space-y-4">
      <ContractComponent
        v-for="character in characters"
        :id="character.character_id"
        :key="character.character_id"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import ContractComponent from "@/Shared/Components/Contracts/ContractComponent.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";

export default {
    name: "Index",
    components: {
      DispatchUpdateButton,
      RequiredScopesWarning,
        ContractComponent,
        EntitySelectionButton,
        PageHeader},
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
        pageTitle: 'Character Contracts'
      }
  },
  methods: {
    getUrl(character_id) {
      return route('character.contracts.details', character_id)
    }
  }
}
</script>

<style scoped>

</style>
