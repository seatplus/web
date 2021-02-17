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
    created() {
        if(_.isUndefined(this.location.location))
            axios.get(this.$route('get.manual_location', this.location.location_id))
                .then((result) => {
                        this.result = result.data
                })
    },
    computed: {
        name() {
            return this.location.location ?? _.get(this.result, 'name', 'loading ...')
        },
        system() {
            return _.get(this.result, 'system.name',)
        }
    }
}
</script>

<style scoped>

</style>
