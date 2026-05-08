<template>
  <WideListElement
    v-for="asset in items"
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
          <span v-else class="inline-flex items-center justify-center h-12 w-12 shrink-0 mx-auto rounded-full bg-gray-500">
              <span class="text-xl font-medium leading-none text-white">N/A</span>
          </span>

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
        :object="ownerObject(asset)"
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
      <span v-if="asset.volume">{{ getMetricPrefix(asset.volume) }}</span>
      <span v-else>N/A</span>
    </template>

    <template #navigation>
      <Link
        v-if="asset.content[0]"
        :href="asset.url"
        preserve-state
        preserve-scroll
      >
          <ChevronRightIcon class="h-5 w-5 text-gray-400" />
      </Link>
        <ChevronRightIcon
          v-else
          class="h-5 w-5 text-transparent"
        />
    </template>
  </WideListElement>
</template>

<script setup>

import WideListElement from "@/Shared/WideListElement.vue";
import EveImage from "@/Shared/EveImage.vue";
import {Link, usePage} from '@inertiajs/vue3';
import { prefix } from 'metric-prefix'
import {computed} from "vue";
import { item } from '@/routes/character'
import {TagIcon, ScaleIcon, ChevronRightIcon} from "@heroicons/vue/20/solid";

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
});

const hasOwnerPicture = computed(() => {

  const urlParams = new URLSearchParams(window.location.search)
  const selectedCharacterIds = urlParams.getAll('character_ids[]')

  if (selectedCharacterIds.length > 1)
    return true

  return !selectedCharacterIds.length && usePage().props.user.data.characters.length > 1;
})

const getMetricPrefix = function (numeric_value) {
    return prefix(numeric_value, {precision: 3, unit: 'm³'})
}

const url = function (asset) {
    return asset.content[0] ? item({item_id: asset.item_id, character_id: asset.owner_id}).url : ''
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

const ownerObject = function (asset) {

  return {
      character_id: asset.owner_id,
      name: 'n/a'
  }
}

</script>
