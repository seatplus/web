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
        <EntitySelectionButton type="corporation" />
      </template>
    </PageHeader>

    <MemberTrackingComponent
      v-for="corporation in corporations"
      :key="corporation.corporation_id"
      :corporation="corporation"
    />
  </div>
</template>

<script>
import PageHeader from "../../../Shared/Layout/PageHeader"
import MemberTrackingComponent from "./MemberTrackingComponent";
import DispatchUpdateButton from "../../../Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "../../../Shared/SidebarLayout/RequiredScopesWarning";
import EntitySelectionButton from "../../../Shared/Components/SlideOver/EntitySelectionButton";

export default {
    name: "MemberTracking",
    components: {
        EntitySelectionButton,
      RequiredScopesWarning,
      DispatchUpdateButton,
      MemberTrackingComponent, PageHeader},
    props: {
        corporations: {
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
        pageTitle: 'Corporation Member Tracking'
      }
  }
}
</script>

<style scoped>

</style>
