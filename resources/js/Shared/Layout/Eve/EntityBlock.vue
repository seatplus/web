<template>
  <div class="flex items-center">
    <div class="shrink-0">
      <EveImage
        :object="entity"
        :size="256"
        :tailwind_class="image_class"
      />
    </div>
    <div class="ml-4">
      <h3 :class="name_class">
        {{ name }}
      </h3>
      <p
        v-if="entity.corporation || entity.alliance"
        class="text-sm text-gray-500 truncate"
      >
        {{ subText }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import EveImage from "@/Shared/EveImage.vue"

const props = defineProps({
    entity: {
        required: true,
        type: Object
    },
    imageSize: {
        required: false,
        default: 12,
        type: Number
    },
    nameFontSize: {
        required: false,
        default: 'lg',
        type: String
    },
    nameClass: {
        required: false,
        type: String,
        default: ''
    }
});

const name = computed(() => _.get(props.entity, 'name', 'missing name'))

const image_class = computed(() => `h-${props.imageSize} w-${props.imageSize} rounded-full`)

const name_class = computed(() => props.nameClass ? props.nameClass : `text-${props.nameFontSize} leading-6 font-medium text-gray-900`)

const subText = computed(() => {
    let alliance = _.get(props.entity, 'alliance', null)

    let names = _.compact([
        _.get(props.entity, 'corporation.name'),
        _.isString(alliance) ? alliance : _.get(alliance, 'name', null),
    ])

    return _.join(names, ' | ')
})
</script>

<style scoped>

</style>
