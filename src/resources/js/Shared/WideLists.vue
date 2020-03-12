<template>
    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-3">
        <slot name="header" />
        <ul>
            <slot name="elements" />
        </ul>
    </div>
</template>

<script>
  export default {
    name: "WideLists"
  }
</script>

<style scoped>

</style>
