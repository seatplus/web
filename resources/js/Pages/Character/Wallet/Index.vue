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
      <WalletFilter v-model="filter" />
      <WalletComponent
        v-for="character_id of character_ids"
        :id="character_id"
        :key="character_id"
        :filters="{ref_type: ref_types}"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import WalletComponent from "@/Shared/Components/Wallet/WalletComponent.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import WalletFilter from "@/Shared/Components/Wallet/WalletFilter.vue";

export default {
    name: "Index",
    components: {
        WalletFilter,
      RequiredScopesWarning,
      DispatchUpdateButton,
        EntitySelectionButton,
        WalletComponent,
        PageHeader
    },
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
    data() {
        return {
          pageTitle: 'Character Wallets',
            entities: [],
            ready: false,
            filter: []
        }
    },
    computed: {
        ref_types() {
            return _.map(this.filter, (ref_type) => ref_type.name)
        }
    }
}
</script>

<style scoped>

</style>
