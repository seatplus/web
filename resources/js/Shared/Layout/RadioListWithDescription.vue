<template>
  <RadioGroup v-model="selected">
    <RadioGroupLabel class="sr-only">
      Privacy setting
    </RadioGroupLabel>
    <div class="bg-white rounded-md -space-y-px">
      <RadioGroupOption
        v-for="(setting, settingIdx) in options"
        :key="setting.title"
        v-slot="{ checked, active }"
        as="template"
        :value="setting"
      >
        <div :class="[settingIdx === 0 ? 'rounded-tl-md rounded-tr-md' : '', settingIdx === options.length - 1 ? 'rounded-bl-md rounded-br-md' : '', checked ? 'bg-indigo-50 border-indigo-200 z-10' : 'border-gray-200', 'relative border p-4 flex cursor-pointer focus:outline-none']">
          <span
            :class="[checked ? 'bg-indigo-600 border-transparent' : 'bg-white border-gray-300', active ? 'ring-2 ring-offset-2 ring-indigo-500' : '', 'h-4 w-4 mt-0.5 cursor-pointer rounded-full border flex items-center justify-center shrink-0']"
            aria-hidden="true"
          >
            <span class="rounded-full bg-white w-1.5 h-1.5" />
          </span>
          <div class="ml-3 flex flex-col">
            <RadioGroupLabel
              as="span"
              :class="[checked ? 'text-indigo-900' : 'text-gray-900', 'block text-sm font-medium capitalize']"
            >
              {{ setting.title }}
            </RadioGroupLabel>
            <RadioGroupDescription
              as="span"
              :class="[checked ? 'text-indigo-700' : 'text-gray-500', 'block text-sm']"
            >
              {{ setting.description }}
            </RadioGroupDescription>
          </div>
        </div>
      </RadioGroupOption>
    </div>
  </RadioGroup>
</template>

<script>
import {ref, watch} from 'vue'
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'

export default {
    name: "RadioListWithDescription",
    components: {
        RadioGroup,
        RadioGroupDescription,
        RadioGroupLabel,
        RadioGroupOption,
    },
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
    setup(props, {emit}) {
        const selected = ref(props.options[props.modelValue])

        watch(selected, (newValue) => {
            emit('update:modelValue', props.options.indexOf(newValue))
        })

        return {
            selected,
        }
    },
    watch: {
        active() {
            this.$emit('update:modelValue', this.id)
        }
    },
}
</script>

<style scoped>

</style>
