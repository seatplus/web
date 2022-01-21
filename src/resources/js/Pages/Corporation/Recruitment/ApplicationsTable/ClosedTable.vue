<template>
  <InfiniteLoadingHelper
    route="closed.corporation.applications"
    :params="{corporation_id: corporationId}"
    @result="(results) => closed = results"
  >
    <ApplicationsTable :applications="closed" />
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";
import {ref} from "vue";
import ApplicationsTable from "@/Pages/Corporation/Recruitment/ApplicationsTable/ApplicationsTable";

export default {
    name: "ClosedTable",
    components: {
        ApplicationsTable,
        InfiniteLoadingHelper},
    props: {
        corporationId: {
            required: true,
            type: Number
        }
    },
    setup() {

        const closed = ref([])

        return {
            closed
        }
    }
}
</script>

<style scoped>

</style>