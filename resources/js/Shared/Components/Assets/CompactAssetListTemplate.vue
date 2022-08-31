<template>
  <li
    :class="[even ? 'bg-gray-50' : 'bg-white', {'cursor-pointer': hasContent}]"
    class="grid grid-cols-2 sm:grid-cols-8 sm:gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-1">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Quantity
      </label>
      {{ entry.quantity }}
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-3">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Type
      </label>
      <div class="flex items-center">
        <div class="flex -space-x-1 relative z-0 overflow-hidden">
          <EveImage
            :object="type"
            :size="256"
            tailwind_class="relative z-10 h-5 w-5 rounded-full"
          />
          <EveImage
            v-if="hasOwnerPicture"
            :tailwind_class="'relative z-0 inline-block h-5 w-5 rounded-full text-white shadow-solid'"
            :object="entry.owner"
            :size="128"
          />
        </div>
        <div class="ml-4">
          <h3 class="text-sm leading-6 font-medium text-gray-900">
            {{ type.name }}
          </h3>
        </div>
      </div>
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-1">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Volume
      </label>
      {{ volume }}
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Group
      </label>
      {{ group }}
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center truncate text-right sm:col-span-1 justify-self-end">
      <span class="sr-only">Expand</span>
      <ChevronRightIcon 
        v-if="hasContent" 
        class="text-gray-400 h-5 w-5 justify-self-end"
      />
    </div>
  </li>
</template>

<script>
import {prefix} from "metric-prefix";
import { ChevronRightIcon } from '@heroicons/vue/solid'
import EveImage from "@/Shared/EveImage.vue"
export default {
    name: "CompactAssetListTemplate",
    components: {EveImage, ChevronRightIcon},
    props: {
        entry: {
            required: true,
            type: Object
        },
        even: {
            required: true,
            type: Number
        }
    },
    computed: {
        name() {

            let type_name = _.get(this.entry, 'type.name', 'missing type information')

            return this.entry.name ? `${this.entry.name} (${type_name})` : type_name
        },
        type() {

            let type = this.entry.type

            type.name = this.name

            return type
        },
        volume() {
            let quantity = this.entry.quantity
            let volume = _.get(this.entry, 'type.volume', 0)

            return this.getMetricPrefix(volume * quantity)
        },
        group() {
            let group_name =  _.get(this.entry, 'type.group.name', 'missing group information')

            return this.entry.is_singleton ? group_name : `${group_name} (packaged)`
        },
        hasContent() {
            return _.size(this.entry.content) > 0
        },
        hasOwnerPicture() {

            let selectedCharacterIds = _.get(route().params, 'character_ids', null)

            if (_.size(selectedCharacterIds) > 1)
                return true

            return !selectedCharacterIds && this.$page.props.user.data.characters.length > 1;
        }
    },
    methods: {
        getMetricPrefix(numeric_value) {

            return prefix(numeric_value, {precision: 3, unit: 'm³'})
        },
    }
}
</script>

<style scoped>

</style>