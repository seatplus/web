<template>
  <div>
    <div class="sm:hidden">
      <label
        for="tabs"
        class="sr-only"
      >
        <slot name="label">
          Select a tab
        </slot>

      </label>
      <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
      <select
        id="tabs"
        name="tabs"
        class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
        @select="(tab) => console.log(tab)"
      >
        <option
          v-for="tab in tabs"
          :key="tab.name"
          :selected="isActive(tab)"
          @click="select(tab)"
        >
          {{ tab.name }}
        </option>
      </select>
    </div>
    <div class="hidden sm:block">
      <nav
        class="relative z-0 rounded-lg shadow flex divide-x divide-gray-200"
        aria-label="Tabs"
      >
        <div
          v-for="(tab, tabIdx) in tabs"
          :key="tab"
          :class="[isActive(tab) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700', tabIdx === 0 ? 'rounded-l-lg' : '', tabIdx === tabs.length - 1 ? 'rounded-r-lg' : '', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10']"
          :aria-current="isActive(tab) ? 'page' : undefined"
          @click="select(tab)"
        >
          <span>{{ tab.name }}</span>
          <span
            aria-hidden="true"
            :class="[isActive(tab) ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
          />
        </div>
      </nav>
    </div>
  </div>
</template>

<script>
import {ref} from "vue";
import {useValidateObject} from "../../../Functions/useValidateObject";

let schema = {
    id: value => _.isInteger(value),
    name: value => _.isString(value),
}

schema.id.required = true
schema.name.required = true

export default {
    name: "BarWithUnderline",
    props: {
        tabs: {
            type: Array,
            required: true,
            validator: tabs => _.chain(tabs)
                .map(tab => useValidateObject(tab, schema))
                .filter()
                .value()
                .length === tabs.length
        },
    },
    emits: ['select'],
    setup(props, {emit}) {
        const activeTab = ref(props.tabs[0])

        const isActive = (entry) => _.isEqual(entry, activeTab.value)
        const select = (tab) => {
            emit('select', tab)
            activeTab.value = tab
        }

        return {
            activeTab,
            isActive,
            select
        }
    }
}
</script>

<style scoped>

</style>