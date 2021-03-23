<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>

  <RequiredScopesWarning :dispatch_transfer_object="dispatch_transfer_object" />

  <PageHeader>
    {{ pageTitle }}
    <template #primary>
      <DispatchUpdateButton />
    </template>
  </PageHeader>

  <MemberTrackingComponent
    v-for="corporation in corporations"
    :key="corporation.corporation_id"
    :corporation="corporation"
  />
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import MemberTrackingComponent from "./MemberTracking/MemberTrackingComponent";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";

export default {
    name: "MemberTracking",
    components: {
      RequiredScopesWarning,
      DispatchUpdateButton,
      MemberTrackingComponent, PageHeader},
    props: {
        corporations: {
            type: Array,
            required: true
        },
        dispatch_transfer_object: {
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
