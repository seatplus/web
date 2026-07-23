<template>
  <div>
    <div class="mt-1 relative rounded-md shadow-xs">
      <select
        v-model="selection"
        :class="['form-select block w-full', error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red' : '']"
      >
        <option
          value=""
          disabled
          selected
        >
          Select your option
        </option>
        <slot />
      </select>
      <div
        v-if="error"
        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
      >
        <svg
          class="h-5 w-5 text-red-500"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
    </div>
    <p
      v-if="error"
      :id="id"
      class="mt-2 text-sm text-red-600"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null,
    },
    id: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const page = usePage();

const selection = ref(props.modelValue)

const error = computed(() => _.get(page, `props.errors[${props.id}][0]`))

watch(selection, (newValue) => {
    emit('update:modelValue', newValue)
})

watch(() => props.modelValue, (newValue) => {
    selection.value = newValue
})
</script>

<style scoped>

</style>
