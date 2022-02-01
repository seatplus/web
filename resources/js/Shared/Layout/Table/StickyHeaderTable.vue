<template>
  <div 
    :class="{'sm:grid-cols-2': countColumns === 2, 'sm:grid-cols-3': countColumns === 3,'sm:grid-cols-4': countColumns === 4, 'sm:grid-cols-5': countColumns === 5, 'sm:grid-cols-6': countColumns === 6, 'sm:grid-cols-7': countColumns === 7, 'sm:grid-cols-8': countColumns === 8, 'sm:grid-cols-9': countColumns === 9, 'sm:grid-cols-10': countColumns === 10, 'sm:grid-cols-11': countColumns === 11, 'sm:grid-cols-12': countColumns === 12}"
    class="hidden z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500 sm:grid sm:gap-1 grid-flow-row "
  >
    <div
      v-for="(title, index) in columns"
      :key="index"
      :class="{'col-span-2': title.columnSpan === 2, 'col-span-3': title.columnSpan === 3, 'col-span-4': title.columnSpan === 4, 'col-span-5': title.columnSpan === 5, 'col-span-6': title.columnSpan === 6, 'col-span-7': title.columnSpan === 7, 'col-span-8': title.columnSpan === 8, 'col-span-9': title.columnSpan === 9, 'col-span-10': title.columnSpan === 10, 'col-span-11': title.columnSpan === 11, 'col-span-12': title.columnSpan === 12}"
      class="px-6 py-1"
    >
      <span :class="{'sr-only' : title.srOnly}">{{ title.label }}</span>
    </div>
  </div>
  <ul class="divide-y divide-gray-200">
    <slot
      :columns="columns"
      :countColumns="countColumns"
    />
  </ul>
</template>

<script>
import {computed} from "vue";
import {useValidateObject} from "@/Functions/useValidateObject";

var schema = {
    title: value => _.isString(value),
    columnSpan: value => _.isInteger(value) && (2 <= value <= 12),
    mobileColumnSpan: value => _.isUndefined(value) ? true : _.isInteger(value) && (2 <= value <= 12),
    srOnly: value => _.isUndefined(value) ? true : _.isBoolean(value)
}

schema.title.required = true
schema.columnSpan.required = true

export default {
    name: "StickyHeaderTable",
    props: {
        headerTitles: {
            required: true,
            type: Array,
            validator: titles => _.chain(titles)
                .map(title => _.isObject(title) ? useValidateObject(title, schema) : _.isString(title))
                .filter()
                .value()
                .length> 0
        }
    },
    setup(props) {

        const countColumns = computed(() => {
            return _.sumBy(props.headerTitles, (title) => {
                return _.isString(title) ? 1 : title.columnSpan
            })
        })

        const columns = computed(() => _.map(props.headerTitles, function (title) {
            return {
                columnSpan: _.get(title, 'columnSpan', 1),
                mobileColumnSpan: _.get(title, 'singleRowElement', false) ? 2 : 1,
                label: _.get(title, 'title', title),
                srOnly: _.get(title, 'srOnly', false),
            }
        }))

        return {
            countColumns,
            columns,
        }
    }
}
</script>

