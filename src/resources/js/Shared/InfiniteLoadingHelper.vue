<template>
  <div>
    <slot />
    <div ref="scrollComponent"></div>
  </div>
</template>

<script>
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
    name: "InfiniteLoadingHelper",
    props: {
        key: {
            type: Number,
            default: () => +new Date()
        },
        route: {
            type: String,
            required: true
        },
        params: {
            required: false
        }
    },
    emits: ['result'],
    setup(props) {

        return useInfinityScrolling(props.route, props.params)
    },
    mounted() {

        this.$emit('result', this.result)
    }
}
</script>

<style scoped>

</style>