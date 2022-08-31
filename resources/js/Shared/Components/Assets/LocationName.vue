<template>
  <span>
    <span v-if="system"> {{ system }} - </span> {{ name }}
  </span>
</template>

<script>
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
        if(_.isNull(this.location.location))
            axios.get(route('get.manual_location', this.location.location_id))
                .then((result) => {
                        this.result = result.data
                })
    }
}
</script>

<style scoped>

</style>
