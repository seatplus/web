<template>
  <div
    :class="{'sm:grid-cols-2': numberColumns === 2, 'sm:grid-cols-3': numberColumns === 3,'sm:grid-cols-4': numberColumns === 4, 'sm:grid-cols-5': numberColumns === 5, 'sm:grid-cols-6': numberColumns === 6, 'sm:grid-cols-7': numberColumns === 7, 'sm:grid-cols-8': numberColumns === 8, 'sm:grid-cols-9': numberColumns === 9, 'sm:grid-cols-10': numberColumns === 10, 'sm:grid-cols-11': numberColumns === 11, 'sm:grid-cols-12': numberColumns === 12}"
    class="bg-white grid grid-cols-2 sm:gap-1 grid-flow-row hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <slot />
  </div>
</template>

<script>
export default {
    name: "StickyHeaderTableRow",
    props: {
        numberColumns: {
            type: Number,
            required: true,
            default: 1
        }
    }
}
</script>

<style scoped>

</style>