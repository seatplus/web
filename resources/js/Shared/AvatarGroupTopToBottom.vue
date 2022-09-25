<template>
  <div class="flex relative z-0 overflow-hidden">
    <img
      v-for="(element, index) in elements"
      :key="index"
      :class="[{'-ml-2' : index > 0, 'z-30': index === 0, 'z-20': index === 1, 'z-10': index === 2, 'z-0': index === 3 },
               'relative inline-block h-8 w-8 rounded-full text-white shadow-solid']"
      :object="element"
      :src="`https://images.evetech.net/characters/${element.character_id}/portrait/?size=${size}&tenant=tranquility`"
    >
  </div>
</template>

<script>

    export default {
        name: "AvatarGroupTopToBottom",
        components: {},
        props: {
            objects: {
                type: Array,
                required: true
            },
            size: {
                type: Number,
                default: 128
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
