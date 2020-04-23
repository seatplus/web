<template>
    <div class="flex overflow-hidden">
        <EveImage v-for="(element,key) in elements" :key="key" :tailwind_class="key > 0 ? '-ml-2' : '' + 'inline-block h-8 w-8 rounded-full text-white shadow-solid'" :object="element" />
    </div>
</template>

<script>
    import EveImage from "./EveImage"
    export default {
        name: "AvatarGroupBottomTop",
        components: {EveImage},
        props: {
            objects: {
                type: Array,
                required: true
            },
            size: {
                type: Number,
                default: 8
            },
            random: {
                type: Boolean,
                default: false
            },
            numberElements: {
                type: Number,
                default: 4
            }
        },
        computed: {
            elements: function () {
                return _.flatten(this.random ? _.sampleSize(this.objects, this.numberElements) : _.reject(this.objects, function (value, key) {
                    return key > this.numberElements
                }))
            }
        }
    }
</script>

<style scoped>

</style>
