<template>
  <div class="flex items-center">
    <div class="flex-shrink-0">
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
        {{ corporationName }}  {{ hasAlliance() ? '| ' + allianceName : '' }}
      </p>
    </div>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage";
export default {
    name: "EntityBlock",
    components: {EveImage},
    props: {
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
        }
    },
    computed: {
        corporationName() {
            return _.get(this.entity, 'corporation.name', '')
        },
        allianceName() {
            return _.get(this.entity, 'alliance.name', '')
        },
        name() {
            return _.get(this.entity, 'name', 'missing name')
        },
        image_class() {
            return `h-${this.imageSize} w-${this.imageSize} rounded-full`
        },
        name_class() {
            return `text-${this.nameFontSize} leading-6 font-medium text-gray-900`
        }
    },
    methods: {
        hasAlliance() {
            return _.has(this.entity, 'alliance.name')
        }
    }
}
</script>

<style scoped>

</style>
