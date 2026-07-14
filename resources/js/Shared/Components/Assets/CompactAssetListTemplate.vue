<template>
  <li
    :class="[even ? 'bg-gray-50' : 'bg-white', {'cursor-pointer': hasContent}]"
    class="relative list-none grid grid-cols-2 sm:grid-cols-8 sm:gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <!-- Whole-row drill-in: stretched-link overlay fills the (relative) row. -->
    <AssetContentsLink
      v-if="hasContent"
      :character-id="entry.owner_id"
      :item-id="entry.item_id"
    />
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
            :object="{character_id: entry.owner_id, name: 'N/A'}"
            :size="128"
          />
        </div>
        <div class="ml-4">
          <!-- Indigo name signals the row is expandable (has contents). -->
          <h3
            class="text-sm leading-6 font-medium"
            :class="hasContent ? 'text-indigo-600' : 'text-gray-900'"
          >
            {{ type.name }}
          </h3>
        </div>
      </div>
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-1">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Volume
      </label>
      <span v-if="entry.volume">{{ getMetricPrefix(entry.volume) }}</span>
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
        class="text-indigo-500 h-5 w-5 justify-self-end"
      />
    </div>
  </li>
</template>

<script setup>
import {prefix} from "metric-prefix";
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import EveImage from "@/Shared/EveImage.vue"
import {computed, defineAsyncComponent} from "vue";
import {usePage} from "@inertiajs/vue3";

// Async to break the ItemList <-> AssetContentsLink import cycle (drilling renders another ItemList).
const AssetContentsLink = defineAsyncComponent(() => import("./AssetContentsLink.vue"));

const props = defineProps({
    entry: {
        required: true,
        type: Object
    },
    even: {
        required: true,
        type: Number
    }
})

const type_name = _.get(props.entry, 'type.name', 'missing type information')
const name = props.entry.name ? `${props.entry.name} (${type_name})` : type_name
const type = {
    ...props.entry.type,
    name: name
}


const group = computed(() => {
    let group_name =  _.get(props.entry, 'type.group.name', 'missing group information')

    return props.entry.is_singleton ? group_name : `${group_name} (packaged)`
})

const hasContent = computed(() => {
    // Unfiltered list sends content_count; the filtered path sends the loaded content array.
    return (props.entry.content_count ?? _.size(props.entry.content)) > 0
})

// Show the owner avatar when the user has more than one character.
const hasOwnerPicture = computed(() => usePage().props.user.data.characters.length > 1)

// Methods
const getMetricPrefix = function (numeric_value) {
    // An asset whose type is unresolved has no volume; metric-prefix/big.js throws
    // "Invalid number" on null/NaN, which would crash the whole render. Guard defensively
    // (the template also v-if's on volume).
    if (numeric_value === null || numeric_value === undefined || isNaN(numeric_value)) {
        return ''
    }

    return prefix(numeric_value, {precision: 3, unit: 'm³'})
}
</script>
