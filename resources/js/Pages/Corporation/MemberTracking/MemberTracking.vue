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
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import MemberTrackingComponent from "./MemberTrackingComponent.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";

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
