<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <RequiredScopesWarning :dispatch_transfer_object="dispatch_transfer_object" />

    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <CharacterSelectionButton />
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
import PageHeader from "@/Shared/Layout/PageHeader";
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
import ContractComponent from "@/Shared/Components/Contracts/ContractComponent";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";

export default {
    name: "Index",
    components: {
      DispatchUpdateButton,
      RequiredScopesWarning,
        ContractComponent,
        CharacterSelectionButton,
        PageHeader},
    props: {
        dispatch_transfer_object: {
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
      return this.$route('character.contracts.details', character_id)
    }
  }
}
</script>

<style scoped>

</style>
