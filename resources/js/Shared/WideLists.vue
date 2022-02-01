<template>
    <div class="bg-white shadow-lg overflow-hidden sm:rounded-md rounded-lg">
        <slot name="header" />
        <ul class="divide-y divide-gray-200">
            <slot name="elements" />
        </ul>
        <slot name="footer" />
    </div>
</template>

<script>
  export default {
    name: "WideLists"
  }
</script>

<style scoped>

</style>
