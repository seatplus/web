<template>
  <span>
    <span v-if="system"> {{ system }} - </span> {{ name }}
  </span>
</template>

<script>
import { getJson } from "@/Functions/http";
import { getLocation } from "@/actions/Seatplus/Web/Http/Controllers/Shared/ManualLocationController";

export default {
    name: "LocationName",
    props: {
        location: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            result: {}
        }
    },
    computed: {
        name() {
            return this.location.location != null ? _.get(this.location, 'location.locatable.name') : _.get(this.result, 'name', 'loading ...')
        },
        system() {
            return _.get(this.result, 'system.name',)
        }
    },
    created() {
        if (_.isNull(this.location.location)) {
            getJson(getLocation.url(this.location.location_id))
                .then((data) => {
                    this.result = data
                })
        }
    }
}
</script>

<style scoped>

</style>
