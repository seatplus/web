<template>
    <span :key="seconds"> {{ getTimeFromNow() }} </span>
</template>

<script>
import dayjs from "dayjs"
import customParseFormat from "dayjs/plugin/customParseFormat"
import relativeTime from "dayjs/plugin/relativeTime"

dayjs.extend(customParseFormat);
dayjs.extend(relativeTime)

export default {
    name: "Time",
    props: {
        timestamp: {
            type: String,
            required: true
        },
        format: {
            type: String,
            required: false
        },
    },
    data() {
        return {
            seconds: 0
        }
    },
    created () {
        this.$nextTick(() => {

            setInterval(()  => {
                this.seconds += 1
            }, 1000)
        })
    },
    methods: {
        getTimeFromNow() {

            return this.format ? dayjs(this.timestamp, this.format).fromNow() : dayjs(this.timestamp).fromNow()
        }
    }
}
</script>

<style scoped>

</style>
