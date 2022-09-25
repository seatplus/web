<template>
  <InfiniteLoadingHelper
    :key="routeParams"
    v-slot="{results}"
    route-name="open.corporation.applications"
    :params="routeParams"
  >
    <ApplicationsTable :applications="filterPendings(results)" />
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";
import ApplicationsTable from "@/Pages/Corporation/Recruitment/ApplicationsTable/ApplicationsTable.vue";

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
    computed: {
        routeParams() {
            return {
                corporation_id: this.corporationId,
                decision_count: this.stepCount
            }
        }
    },
    methods: {
        filterPendings(pendings) {
            return _.filter(pendings, {decision_count: this.stepCount})
        }
    }
}
</script>

<style scoped>

</style>