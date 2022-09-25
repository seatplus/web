<template>
  <div>
    <slot name="label">
      <label
        :for="label"
        class="block text-sm font-medium text-gray-700"
      >{{ label }}</label>
    </slot>
    <div class="relative mt-1 rounded-md shadow-sm">
      <input
        :id="label"
        :type="type"
        :name="label"
        :value="modelValue"
        :class="[
          'block w-full rounded-md pr-10 focus:outline-none sm:text-sm',
          {'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : error},
          {'border-yellow-300 text-yellow-900 placeholder-yellow-300 focus:border-yellow-500 focus:ring-yellow-500' : warning},
          {'border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500' : isNormal},
        ]"
        :placeholder="placeholder"
        @input="emit('update:modelValue', $event.target.value)"
      >
      <div
        v-if="error || warning"
        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
      >
        <ExclamationCircleIcon
          class="h-5 w-5"
          :class="{'text-red-500' : error, 'text-yellow-500' : warning}"
          aria-hidden="true"
        />
      </div>
    </div>
    <slot name="description">
      <p
        v-show="error || warning"
        class="mt-2 text-sm"
        :class="{'text-red-600' : error, 'text-yellow-600' : warning}"
      >
        {{ description }}
      </p>
    </slot>
  </div>
</template>

<script setup>
import {ExclamationCircleIcon} from '@heroicons/vue/20/solid'
import {computed} from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'text',
        validator(value) {
            return ['text', 'email', 'password'].includes(value)
        }
    },
    placeholder: {
        type: String,
        required: false,
        default: '',
    },
    error: {
        type: String,
        required: false,
        default: '',
    },
    warning: {
        type: String,
        required: false,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])
const isNormal = computed(() => !(props.error && props.warning))
const description = computed(() => {
    let arr = [props.error, props.warning]

    return arr.filter((item) => item !== '').join(' ')
})

</script>

