<template>
  <Listbox
    v-model="selected"
    as="div"
    multiple
    by="id"
  >
    <ListboxLabel class="block text-sm font-medium leading-5 text-gray-700">
      {{ label }}
    </ListboxLabel>
    <div class="mt-1 relative">
      <ListboxButton class="relative w-full bg-white border border-gray-300 rounded-md shadow-xs pl-3 pr-10 py-2 text-left cursor-default focus:outline-hidden focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <span
          class="block truncate"
          :class="{ 'text-gray-400': ! selected.length }"
        >{{ buttonLabel }}</span>
        <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <ListboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black/5 overflow-auto focus:outline-hidden sm:text-sm">
          <li
            v-if="! options.length"
            class="px-3 py-2 text-sm text-gray-500 select-none"
          >
            {{ emptyLabel }}
          </li>
          <ListboxOption
            v-for="option in options"
            :key="option.id"
            v-slot="{ active, selected: isSelected }"
            :value="option"
            as="template"
          >
            <li :class="[active ? 'text-white bg-indigo-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-8 pr-4']">
              <span :class="[isSelected ? 'font-semibold' : 'font-normal', 'block truncate']">
                {{ option.name }}
              </span>
              <span
                v-if="isSelected"
                :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 left-0 flex items-center pl-1.5']"
              >
                <CheckIcon class="h-5 w-5" />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>

<script setup>
import { computed } from "vue";
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    // list of { id, name }
    options: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        default: "All",
    },
    emptyLabel: {
        type: String,
        default: "none available",
    },
});

const emit = defineEmits(["update:modelValue"]);

const selected = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const buttonLabel = computed(() => (selected.value.length
    ? selected.value.map((option) => option.name).join(", ")
    : props.placeholder));
</script>
