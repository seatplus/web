<template>
  <InfiniteLoadingHelper
    v-slot="{results}"
    route="open.corporation.applications"
    :params="{corporation_id: corporationId}"
  >
    <ApplicationsTable :applications="filterPendings(results)" />
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";
import ApplicationsTable from "@/Pages/Corporation/Recruitment/ApplicationsTable/ApplicationsTable";

export default {
    name: "PendingTable",
    components: {
        ApplicationsTable,
         InfiniteLoadingHelper,
    },
    props: {
        stepCount: {
            required: true,
            type: Number
        },
        corporationId: {
            required: true,
            type: Number
        }
    },
    methods: {
        filterPendings(pendings) {
            return _.filter(pendings, {decision_count: this.stepCount})
        }
    },
}
</script>

<style scoped>

</style>