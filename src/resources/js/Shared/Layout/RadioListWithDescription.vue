<template>
  <fieldset>
    <legend class="sr-only">
      {{ title }}
    </legend>

    <div
      ref="radiogroup"
      class="bg-white rounded-md -space-y-px"
    >
      <div
        v-for="(option, index) in options"
        :key="index"
        :class="[active === index ? 'bg-indigo-50 border-indigo-200 z-10' : 'border-gray-200', { 'rounded-tl-md rounded-tr-md': index === 0, 'rounded-bl-md rounded-br-md': index === options.length-1}]"
        class="relative border p-4 flex cursor-pointer"
        @click="select(index)"
      >
        <div class="flex items-center h-5">
          <input
            :id="title +'-settings-option-' + index"
            name="privacy_setting"
            type="radio"
            class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out cursor-pointer"
            :checked="active === index"
            @keydown.space="select(index)"
            @keydown.arrow-up="onArrowUp(index)"
            @keydown.arrow-down="onArrowDown(index)"
          >
        </div>
        <label
          :for="title +'-settings-option-' + index"
          class="ml-3 flex flex-col cursor-pointer"
        >
          <span
            :class="{ 'text-indigo-900': active === index, 'text-gray-900': !(active === index) }"
            class="block text-sm leading-5 font-medium capitalize"
          >
            {{ option.title }}
          </span>
          <span
            :class="{ 'text-indigo-700': active === index, 'text-gray-500': !(active === index) }"
            class="block text-sm leading-5"
          >
            {{ option.description }}
          </span>
        </label>
      </div>
    </div>
  </fieldset>
</template>

<script>
export default {
  name: "RadioListWithDescription",
  props: {
    title: {
      type: String,
      default: 'Default'
    },
    options: {
      type: Array,
      default: () => [
        {title: 'Title', description: 'description'},
        {title: 'Title1', description: 'description'},
        {title: 'Title2', description: 'description'}
      ]
    },
    modelValue: {
      type: Number,
      default: 0
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      active: this.modelValue
    }
  },
  computed: {
    id() {
      return _.get(this.options[this.active], 'id', this.active)
    }
  },
  watch: {
    active() {
      this.$emit('update:modelValue', this.id)
    }
  },
  methods: {
    select(num1) {
      this.active = num1
    },
    onArrowUp(num1) {
      this.active = num1--
    },
    onArrowDown(num1) {
      this.active = num1++
    }
  }
}
</script>

<style scoped>

</style>
