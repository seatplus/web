<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <Listbox
    v-model="selectedOption"
    as="div"
  >
    <ListboxLabel
      v-if="listLabel"
      class="block text-sm font-medium text-gray-700"
    >
      {{ listLabel }}
    </ListboxLabel>
    <div class="mt-1 relative">
      <ListboxButton class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <span class="flex items-center">
          <EveImage
            :object="selectedOption"
            tailwind_class="shrink-0 h-6 w-6 rounded-full"
          />
          <span class="ml-3 block truncate">{{ selectedOption.name }}</span>
        </span>
        <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <SelectorIcon
            class="h-5 w-5 text-gray-400"
            aria-hidden="true"
          />
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <ListboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
          <ListboxOption
            v-for="option in options"
            :key="option.id"
            v-slot="{ active, selected }"
            as="template"
            :value="option"
          >
            <li :class="[active ? 'text-white bg-indigo-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
              <div class="flex items-center">
                <EveImage
                  :object="option"
                  tailwind_class="shrink-0 h-6 w-6 rounded-full"
                />
                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                  {{ option.name }}
                </span>
              </div>

              <span
                v-if="selected"
                :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
              >
                <CheckIcon
                  class="h-5 w-5"
                  aria-hidden="true"
                />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>

<script>
import {ref, watch} from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'
import EveImage from "../EveImage.vue";

export default {
    components: {
        EveImage,
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        CheckIcon,
        SelectorIcon,
    },
    props: {
        listLabel: {
            required: false,
            type: String
        },
        selected: {
            required: true,
            type: Object
        },
        options: {
            required: true,
            type: Array
        }
    },
    emits: ['update:selected'],
    setup(props, {emit}) {

        const selectedOption = ref(_.find(props.options, option =>_.isEqual(option, props.selected)))

        watch(selectedOption, (newValue) => emit('update:selected',newValue))

        return {
            selectedOption,
        }
    },
}
</script>