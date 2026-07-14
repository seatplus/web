<template>
  <div>
    <Combobox
      v-model="selected"
      as="div"
      multiple
      by="id"
    >
      <ComboboxLabel class="block text-sm font-medium leading-5 text-gray-700">
        {{ label }}
      </ComboboxLabel>
      <div class="mt-1 relative">
        <ComboboxInput
          class="w-full bg-white border border-gray-300 rounded-md shadow-xs pl-3 pr-10 py-2 text-left focus:outline-hidden focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          :placeholder="placeholder"
          :display-value="() => ''"
          autocomplete="off"
          @change="query = $event.target.value"
        />
        <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
        </ComboboxButton>

        <transition
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
          @after-leave="query = ''"
        >
          <ComboboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black/5 overflow-auto focus:outline-hidden sm:text-sm">
            <div
              v-if="! filteredOptions.length"
              class="px-3 py-2 text-sm text-gray-500 select-none"
            >
              {{ options.length ? 'no match' : emptyLabel }}
            </div>
            <ComboboxOption
              v-for="option in filteredOptions"
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
            </ComboboxOption>
          </ComboboxOptions>
        </transition>
      </div>
    </Combobox>

    <!-- Selected options as removable pills below the input. -->
    <div
      v-if="selected.length"
      class="mt-2 flex flex-wrap gap-1"
    >
      <DismissibleButton
        v-for="option in selected"
        :id="option.id"
        :key="option.id"
        :name="option.name"
        @remove="remove"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { Combobox, ComboboxButton, ComboboxInput, ComboboxLabel, ComboboxOption, ComboboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";

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
        default: "Search…",
    },
    emptyLabel: {
        type: String,
        default: "none available",
    },
});

const emit = defineEmits(["update:modelValue"]);

const query = ref("");

const selected = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const filteredOptions = computed(() => {
    const needle = query.value.trim().toLowerCase();
    if (! needle) {
        return props.options;
    }

    return props.options.filter((option) => option.name.toLowerCase().includes(needle));
});

const remove = (id) => {
    selected.value = selected.value.filter((option) => option.id !== id);
};
</script>
