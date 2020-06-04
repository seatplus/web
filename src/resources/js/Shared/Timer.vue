<template>
    <span>{{ timeFromNow }}</span>
</template>

<script>

    import relativeTime from 'dayjs/plugin/relativeTime'
    import dayjs from 'dayjs'

    dayjs.extend(relativeTime)

    export default {
        name: "Timer",
        props: ['created'],
        data() {
            return {
                timeFromNow: null
            }
        },
        created () {
            this.$nextTick(() => {
                this.getTimeFromNow()
                setInterval(this.getTimeFromNow, 1000)
            })
        },
        destroyed () {
            clearInterval(this.getTimeFromNow)
        },
        methods: {
            getTimeFromNow () {
                this.timeFromNow = dayjs.unix(this.created).fromNow()
            }
        }
    }
</script>

<style scoped>

</style>
