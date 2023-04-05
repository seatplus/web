<template>
  <WideListElement
    v-for="asset in props.items"
    :key="asset.item_id"
    :url="url(asset)"
  >
    <template #avatar>
      <span class="inline-block relative">
        <EveImage
          v-if="asset.type"
          :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
          :object="asset.type"
          :size="128"
        />
        <div v-else>
          <span class="inline-flex items-center justify-center h-12 w-12 shrink-0 mx-auto rounded-full bg-gray-500">
            <span class="text-xl font-medium leading-none text-white">N/A</span>
          </span>
        </div>
        <span
          v-if="asset.quantity > 1"
          class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400"
        >
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ asset.quantity }}</span>
        </span>
      </span>
      <EveImage
        v-if="hasOwnerPicture"
        :tailwind_class="'-ml-1 inline-block h-12 w-12 rounded-full text-white shadow-solid'"
        :object="asset.owner"
        :size="128"
      />
    </template>

    <template #upper_left>
      {{ asset.name }}
    </template>

    <template #lower_left>
      <TagIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />

      <span class="truncate">{{ getType(asset).name }}</span>
    </template>

    <template #upper_right>
      {{ getType(asset).group.name }} <span
        v-if="!asset.is_singleton"
        class="text-info"
      >(packaged)</span>
    </template>

    <template #lower_right>
      <ScaleIcon class="shrink-0 mr-1.5 h-5 w-5" />
      <span v-if="asset.type">{{ getMetricPrefix(asset.quantity * asset.type.volume) }}</span>
      <span v-else>N/A</span>
    </template>

    <template #navigation>
      <Link
        v-if="asset.content[0]"
        :href="route('character.item', {item_id: asset.item_id, character_id: asset.owner.character_id})"
        preserve-state
        preserve-scroll
      >
        <svg
          :class="[{'text-gray-400' : asset.content[0], 'text-transparent' : !asset.content[0]},'h-5 w-5']"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </Link>
    </template>
  </WideListElement>
</template>

<script setup>

import WideListElement from "@/Shared/WideListElement.vue";
import EveImage from "@/Shared/EveImage.vue";
import {Link, usePage} from '@inertiajs/vue3';
import { prefix } from 'metric-prefix'
import {computed} from "vue";
import {TagIcon, ScaleIcon} from "@heroicons/vue/20/solid";

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
});

const hasOwnerPicture = computed(() => {

  let selectedCharacterIds = _.get(route().params, 'character_ids', null)

  if (_.size(selectedCharacterIds) > 1)
    return true

  return !selectedCharacterIds && usePage().props.user.data.characters.length > 1;
})

const getMetricPrefix = function (numeric_value) {

  return prefix(numeric_value, {precision: 3, unit: 'm³'})
}

const url = function (asset) {

  return asset.content[0] ? route('character.item', {item_id: asset.item_id, character_id: asset.owner.character_id}) : ''
}

const getType = function (asset) {

  let type = asset.type

  // if type is not set we createa a dummy type
  if (!type) {
    type = {
      name: 'Unknown',
      group: {
        name: 'Unknown'
      }
    }
  }

  return type
}

</script>

<style scoped>

</style>