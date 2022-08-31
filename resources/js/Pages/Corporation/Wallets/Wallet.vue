<template>
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
      <EntitySelectionButton type="corporation" />
    </template>
  </PageHeader>

  <div class="space-y-4">
    <WalletComponent
      v-for="wallet_division of corporationDivisions"
      :id="wallet_division.corporation_id"
      :key="`corporation: ${wallet_division.corporation_id} | division ${wallet_division.division_id}`"
      :division="wallet_division"
    />
  </div>
</template>

<script>
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import WalletComponent from "@/Shared/Components/Wallet/WalletComponent.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";

export default {
    name: "Wallet",
    components: {EntitySelectionButton, WalletComponent, DispatchUpdateButton, PageHeader, RequiredScopesWarning},
    props: {
        corporationDivisions: {
            type: Array,
            required: true
        },
        dispatchTransferObject: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            pageTitle: 'Corporation Wallets'
        }
    }
}
</script>

<style scoped>

</style>