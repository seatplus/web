<template>
  <InfiniteLoadingHelper
    v-slot="{results}"
    route="closed.corporation.applications"
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
import InfiniteLoadingHelper from "../../../../Shared/InfiniteLoadingHelper";
import {ref} from "vue";
import ApplicationsTable from "./ApplicationsTable";
import ActivityLogModal from "./ActivityLogModal";

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