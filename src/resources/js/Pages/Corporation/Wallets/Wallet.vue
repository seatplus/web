<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>

  <RequiredScopesWarning :dispatch_transfer_object="dispatchTransferObject" />

  <PageHeader>
    {{ pageTitle }}
    <template #primary>
      <DispatchUpdateButton />
    </template>
    <template #secondary>
      <EntitySelectionButton type="corporation"/>
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
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import PageHeader from "@/Shared/Layout/PageHeader";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import WalletComponent from "@/Shared/Components/Wallet/WalletComponent";
import EntitySelectionButton from "../../../Shared/Components/SlideOver/EntitySelectionButton";

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