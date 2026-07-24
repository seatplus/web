<template>
  <Listbox v-model="selected">
    <ListboxLabel
      v-if="label"
      class="block text-sm font-medium text-gray-700"
    >
      {{ label }}
    </ListboxLabel>
    <input
      :id="label"
      v-model="query"
      type="text"
      :name="label"
      class="shadow-xs focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
      :placeholder="placeholder"
      @click="toggle"
      @keyup.delete="handleBackspace"
    >
    <div v-show="open">
      <div
        class="absolute inset-0 bg-transparent"
        @click="toggle"
      />
      <ListboxOptions
        static
        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black/5 overflow-auto focus:outline-hidden sm:text-sm"
      >
        <ListboxOption
          v-for="option in options"
          :key="option"
          v-slot="{ selected: isSelected }"
          :value="option"
          class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-8 pr-4"
        >
          <EntityBlock
            v-if="option.hasEveImage"
            :entity="option"
            class="block truncate"
            :image-size="5"
            :name-class="isSelected ? 'font-semibold' : 'font-medium' + ' ' + 'text-sm leading 6 text-gray-900'"
          />
          <div v-else>
            {{ option.name }}
          </div>
          <span
            v-show="isSelected"
            class="absolute inset-y-0 left-0 flex items-center pl-1.5"
          >
            <CheckIcon class="h-5 w-5" />
          </span>
          <ListboxOption />
        </listboxoption>
      </ListboxOptions>
    </div>
  </listbox>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import {CheckIcon} from "@heroicons/vue/20/solid";
import {
    Listbox,
    ListboxOptions,
    ListboxOption,
    ListboxLabel
} from '@headlessui/vue'
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { getJson } from "@/Functions/http";
import { typesOrGroupsOrCategories } from "@/actions/Seatplus/Web/Http/Controllers/Shared/HelperController";

defineProps({
    label: {
        required: false,
        type: String,
        default: null
    },
    placeholder: {
        required: true,
        type: String
    }
});

const emit = defineEmits(['selected', 'selectedObject']);

const selected = ref(null)
const query = ref('')
const suggestions = ref([])
const open = ref(false)

const options = computed(() => _.isArray(suggestions.value) ? suggestions.value : _.get(suggestions.value, 'data', []))

function toggle() {
    if (options.value.length > 0)
        open.value = !open.value
}

function handleBackspace() {
    if (query.value.length > 2)
        return;

    open.value = false
    suggestions.value = []
    selected.value = null
    emit('selected', null)
}

watch(query, (newQuery) => {

    if (newQuery === undefined) {
        return;
    }

    // In case of a select, the query gets updated, we need to prevent the suggestions from showing again.
    if (newQuery === _.get(selected.value, 'name')) {
        return;
    }

    if (newQuery.length <= 2)
        return;

    return getJson(typesOrGroupsOrCategories.url({ query: { search: newQuery } }))
        .then((result) => {
            suggestions.value = result

            // if previously the suggestions were not shown toggle them
            if (!open.value)
                toggle()
        })
})

watch(selected, (newValue) => {
    query.value = _.get(newValue, 'name')
    open.value = false
    emit('selected', _.get(newValue, 'id'))
    emit('selectedObject', newValue)
})
</script>

<style scoped>

</style>
