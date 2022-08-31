<template>
  <ul class="divide-y divide-gray-200 overflow-y-auto">
    <InfiniteLoadingHelper
      v-slot="{results}"
      route="manual_job.entities"
      method="POST"
      :params="dispatch_transfer_object">
      <DispatchableEntry
              v-for="(entity, index) of results"
              :key="`dispatchable entry ${index}`"
              :entry="entity"
      />
    </InfiniteLoadingHelper>
  </ul>
</template>

<script>

import DispatchableEntry from "./DispatchableEntry";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";
import EveImage from "@/Shared/EveImage.vue";

export default {
    name: "DispatchUpdate",
    components: {EveImage, InfiniteLoadingHelper, DispatchableEntry},
    data: function () {
        return {
            //job_name: this.$page.props.dispatch_transfer_object.manual_job,
            //dispatch_transfer_object: this.$page.props.dispatch_transfer_object,
            entities: [],
            infiniteId: new Date()
        }
    },
    created() {

        /*if(!this.dispatch_transfer_object)
            this.dispatch_transfer_object = this.$page.props.dispatchTransferObject*/

        this.getEntities();
    },
    methods: {
        dispatchJob(entity) {

            if(entity.batch !== 'ready')
                return

            axios.post(this.route('dispatch.job', {
                character_id: entity.character_id,
                corporation_id: entity.corporation_id,
            }), {
                dispatch_transfer_object: this.dispatch_transfer_object
            })

            setTimeout(() => this.getEntities(), 100)


        },
        async getEntities() {
            //TODO refactor this
            //axios.post(this.route('manual_job.entities'), this.dispatch_transfer_object)
            //    .then((response) => {
            //        this.entities = response.data
            //        this.infiniteId++
            //    })
        }
    },
    computed: {
        dispatch_transfer_object() {
            return this.$page.props.dispatch_transfer_object != null ? this.$page.props.dispatch_transfer_object : this.$page.props.dispatchTransferObject
        },
        route() {
            return this.route('manual_job.entities')
        },
        job_name() {
            return _.get(this.dispatch_transfer_object, 'manual_job')
        }
    }
}
</script>

<style scoped>

</style>
