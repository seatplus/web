<template>
  <div>
    <div class="sm:hidden">
      <label
        for="tabs"
        class="sr-only"
      >Select a tab</label>
      <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
      <select
        id="tabs"
        name="tabs"
        class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option
          v-for="tab in tabs"
          :key="tab.name"
          :selected="tab.current"
          :value="tab.value"
        >
          {{ tab.name }}
        </option>
      </select>
    </div>
    <div class="hidden sm:block">
      <nav
        class="flex space-x-4"
        aria-label="Tabs"
      >
        <button
          v-for="tab in tabs"
          :key="tab.name"
          :class="[tab.current ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700', 'px-3 py-2 font-medium text-sm rounded-md']"
          :aria-current="tab.current ? 'page' : undefined"
          @click="$emit('update:modelValue', tab.value)"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>
  </div>
</template>

<script>
import {computed} from "vue";

const raw_tabs = [
    { name: 'Default', value: 'default' },
    { name: 'Renegades only', value: 'renegades'},
    { name: 'Loyalists only', value: 'loyalists' },
]

export default {
    name: "ComplianceTabs",
    props: {
        modelValue: {
            type: String,
            required: true,
            default: 'default'
        }
    },
emits: ['update:modelValue'],
    setup(props) {

        const tabs = computed(() => _.map(raw_tabs, function (tab) {

            tab.current = _.isEqual(tab.value, props.modelValue)
            return tab
        }))

        return {
            tabs,
        }
    },
}
</script>