<template>
  <ul class="divide-y divide-gray-200 overflow-y-auto">
    <InfiniteLoadingHelper
      v-slot="{results}"
      :url="entities().url"
      method="POST"
      :post-data="dispatch_transfer_object"
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
import { job as dispatchJob } from '@/routes/dispatch'
import { entities } from '@/routes/manual_job'

export default {
    name: "DispatchUpdate",
    components: {InfiniteLoadingHelper, DispatchableEntry},
    setup() {
        return { dispatchJob, entities }
    },
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

            axios.post(dispatchJob({ query: {
                character_id: entity.character_id,
                corporation_id: entity.corporation_id,
            }}).url, {
                dispatch_transfer_object: this.dispatch_transfer_object
            })
        }
    }
}
</script>
