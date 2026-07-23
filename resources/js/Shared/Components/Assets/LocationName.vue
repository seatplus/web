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
            // An already-resolved name (a location summary carries `name` directly) wins, so a
            // known location renders immediately without an on-demand fetch.
            if (this.location.name) {
                return this.location.name
            }

            return this.location.location != null ? _.get(this.location, 'location.locatable.name') : _.get(this.result, 'name', 'loading ...')
        },
        system() {
            return _.get(this.result, 'system.name',)
        }
    },
    created() {
        // Only resolve on-demand when there is no known name and no eager relation to read from.
        if (! this.location.name && this.location.location == null) {
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
