<template>
  <fieldset class="mt-4">
    <legend class="sr-only">
      {{ legend }}
    </legend>
    <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
      <div
        v-for="option in options"
        :key="option.id"
        class="flex items-center"
      >
        <input
          :id="option.id"
          v-model="picked"
          :name="key +option.id"
          :value="option.id"
          type="radio"
          class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
        >
        <label
          :for="option.id"
          class="ml-3 block text-sm font-medium text-gray-700"
        >
          {{ option.title }}
        </label>
      </div>
    </div>
  </fieldset>
</template>

<script>

import {getCurrentInstance} from "vue";

export default {
    name: "SimpleInlineList",
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        options: {
            required: false,
            type: Array,
            default: () => []
        },
        legend: {
            required: false,
            type: String,
            default: 'legend'
        }
    },
    emits: ['update:modelValue'],
    setup() {

    },
    data() {
        return {
            picked: this.modelValue
        }
    },
    computed: {
        key() {
            return getCurrentInstance().vnode.key
        }
    },
    watch: {
        picked(newValue) {
            this.$emit('update:modelValue', newValue)
        }
    }
}
</script>

<style scoped>

</style>