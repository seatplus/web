<template>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-grey-200">
            <div class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                <slot />
            </div>
        </ul>
    </div>
</template>

<script>
  import ListElement from "./ListElement"
  export default {
    name: "List",
      components: {ListElement}
  }
</script>

<style scoped>

</style>
