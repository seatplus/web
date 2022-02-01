<template>
  <div
    :class="{'col-span-1': enrichedCell.mobileColumnSpan === 1, 'col-span-2': enrichedCell.mobileColumnSpan === 2, 'sm:col-span-2': enrichedCell.columnSpan === 2, 'sm:col-span-3': enrichedCell.columnSpan === 3,'sm:col-span-4': enrichedCell.columnSpan === 4, 'sm:col-span-5': enrichedCell.columnSpan === 5, 'sm:col-span-6': enrichedCell.columnSpan === 6, 'sm:col-span-7': enrichedCell.columnSpan === 7, 'sm:col-span-8': enrichedCell.columnSpan === 8, 'sm:col-span-9': enrichedCell.columnSpan === 9, 'sm:col-span-10': enrichedCell.columnSpan === 10, 'sm:col-span-11': enrichedCell.columnSpan === 11, 'sm:col-span-12': enrichedCell.columnSpan === 12}"
    class="px-6 py-4 sm:py-1"
  >
    <label class="block text-sm font-medium text-gray-700 sm:hidden">
      {{ enrichedCell.label }}
    </label>
    <slot />
  </div>
</template>

<script>
import {useValidateObject} from "../../../Functions/useValidateObject";
import {computed} from "vue";

var schema = {
    label: value => _.isString(value),
    columnSpan: value => _.isInteger(value) && (2 <= value <= 12),
    mobileColumnSpan: value => _.isInteger(value) && (2 <= value <= 12),
}

schema.label.required = true
schema.columnSpan.required = true

export default {
    name: "StickyHeaderCell",
    props: {
        cell: {
            required: true,
            type: [String, Object],
            validator: cell => _.isObject(cell) ? useValidateObject(cell, schema) : _.isString(cell)
        },
        mobileColumnSpan: {
            required: false,
            type: Number,
            default: 1
        }
    },
    setup(props) {
        const enrichedCell = computed(() => {
            return {
                label: _.isString(props.cell) ? props.cell : props.cell.label,
                columnSpan: _.isString(props.cell) ? 1 : props.cell.columnSpan,
                mobileColumnSpan: _.isString(props.cell) ? 1 : _.get(props.cell, 'mobileColumnSpan', props.mobileColumnSpan),
            }
        })

        return {
            enrichedCell
        }
    }
}
</script>

<style scoped>

</style>