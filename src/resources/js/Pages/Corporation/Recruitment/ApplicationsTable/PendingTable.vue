<template>
  <InfiniteLoadingHelper
    :key="routeParams"
    route="open.corporation.applications"
    :params="routeParams"
    @result="(results) => raw_pending = results"
  >
    <ApplicationsTable :applications="pending" />
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";
import {computed, ref} from "vue";
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
    setup(props) {

        const raw_pending = ref([])

        const pending = computed(() => _.filter(raw_pending.value, {decision_count: props.stepCount}))

        const routeParams = computed(() => {
            return {
                corporation_id: props.corporationId,
                decision_count: props.stepCount
            }
        })

        return {
            raw_pending,
            pending,
            routeParams
        }
    }
}
</script>

<style scoped>

</style>