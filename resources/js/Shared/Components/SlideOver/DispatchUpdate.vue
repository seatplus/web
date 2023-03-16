<template>
  <ul class="divide-y divide-gray-200 overflow-y-auto">
    <InfiniteLoadingHelper
      v-slot="{results}"
      route-name="manual_job.entities"
      method="POST"
      :params="dispatch_transfer_object"
    >
      <DispatchableEntry
        v-for="(entity, index) of results"
        :key="`dispatchable entry ${index}`"
        :entry="entity"
      />
    </InfiniteLoadingHelper>
  </ul>
</template>

<script>

import DispatchableEntry from "./DispatchableEntry.vue";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";

export default {
    name: "DispatchUpdate",
    components: {InfiniteLoadingHelper, DispatchableEntry},
    computed: {
        dispatch_transfer_object() {
            return this.$page.props.dispatch_transfer_object != null ? this.$page.props.dispatch_transfer_object : this.$page.props.dispatchTransferObject
        },
        job_name() {
            return _.get(this.dispatch_transfer_object, 'manual_job')
        }
    },
    methods: {
        dispatchJob(entity) {

            if(entity.batch !== 'ready')
                return

            axios.post(route('dispatch.job', {
                character_id: entity.character_id,
                corporation_id: entity.corporation_id,
            }), {
                dispatch_transfer_object: this.dispatch_transfer_object
            })
        }
    }
}
</script>
