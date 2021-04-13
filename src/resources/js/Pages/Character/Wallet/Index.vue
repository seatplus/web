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
        <EntitySelectionButton />
      </template>
    </PageHeader>


    <div class="space-y-4">
      <WalletComponent
        v-for="character_id of character_ids"
        :id="character_id"
        :key="character_id"
      />
    </div>
  </div>
</template>

<script>
import Layout from "@/Shared/SidebarLayout/Layout";
import PageHeader from "@/Shared/Layout/PageHeader";
import WalletComponent from "../../../Shared/Components/Wallet/WalletComponent";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";

export default {
    name: "Index",
    components: {
      RequiredScopesWarning,
      DispatchUpdateButton,
        EntitySelectionButton,
        WalletComponent,
        PageHeader
    },
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
        character_ids: {
            required: true,
            type: Array
        }
    },
  layout: (h, page) => h(Layout, { dispatch_transfer_object: page.props.dispatch_transfer_object }, [page]),
    data() {
        return {
          pageTitle: 'Character Wallets',
            entities: [],
            ready: false
        }
    },
    created: function () {

    },
    methods: {
        openSlideOver(value) {
            //TODO this.$eventBus.$emit('open-slideOver', value);
        },
    }
}
</script>

<style scoped>

</style>
