<template>
    <div class="bg-white shadow overflow-hidden sm:rounded-md rounded-lg mb-3">
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
