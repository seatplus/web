<template>
  <span :key="seconds"> {{ getTimeFromNow() }} </span>
</template>

<script setup>
import { nextTick, ref } from "vue"
import dayjs from "dayjs"
import customParseFormat from "dayjs/plugin/customParseFormat"
import relativeTime from "dayjs/plugin/relativeTime"

dayjs.extend(customParseFormat);
dayjs.extend(relativeTime)

const props = defineProps({
    timestamp: {
        type: String,
        required: true
    },
    format: {
        type: String,
        required: false,
        default: null
    },
});

const seconds = ref(0)

nextTick(() => {
    setInterval(()  => {
        seconds.value += 1
    }, 1000)
})

function getTimeFromNow() {
    return props.format ? dayjs(props.timestamp, props.format).fromNow() : dayjs(props.timestamp).fromNow()
}
</script>

<style scoped>

</style>
