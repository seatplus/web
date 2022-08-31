<template>
  <InfiniteLoadingHelper
    v-slot="{results}"
    route-name="closed.corporation.applications"
    :params="{corporation_id: corporationId}"
  >
    <ApplicationsTable :applications="results">
      <template #default="{ applicant }">
        <ActivityLogModal :application-id="applicant.application_id" />
      </template>
    </ApplicationsTable>
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";
import ApplicationsTable from "./ApplicationsTable.vue";
import ActivityLogModal from "./ActivityLogModal.vue";

export default {
    name: "ClosedTable",
    components: {
        ActivityLogModal,
        ApplicationsTable,
        InfiniteLoadingHelper},
    props: {
        corporationId: {
            required: true,
            type: Number
        }
    }
}
</script>

<style scoped>

</style>