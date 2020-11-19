<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="border-b border-gray-200 "><!--px-4 py-5 sm:px-6-->
            <!-- Content goes here -->
            <slot name="header" />
            <!-- We use less vertical padding on card headers on desktop than on body sections -->
        </div>
        <div><!--class="px-4 py-5 sm:p-6"-->
            <!-- Content goes here -->
            <slot />
        </div>
    </div>
</template>

<script>
export default {
name: "CardWithHeader"
}
</script>

<style scoped>

</style>
