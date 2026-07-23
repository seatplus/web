<template>
  <span
    :class="trackClass"
    class="text-left relative inline-block flex-no-shrink h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-hidden focus:ring-3"
    role="checkbox"
    tabindex="0"
    :aria-checked="modelValue.toString()"
    @click="toggle()"
    @keydown.space.prevent="toggle()"
  >
    <span
      aria-hidden="true"
      :class="{ 'translate-x-5': modelValue, 'translate-x-0': !modelValue }"
      class="inline-block h-5 w-5 rounded-full bg-white shadow-sm transform transition ease-in-out duration-200"
    />
  </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    bgClass: {
        type: String,
        default: 'bg-indigo-600'
    },
    modelValue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const trackClass = computed(() => props.modelValue ? props.bgClass : 'bg-gray-200')

function toggle() {
    emit('update:modelValue', !props.modelValue);
}
</script>
