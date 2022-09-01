<template>
  <DarkSidebar>
    <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
      <!-- Start main area-->
      <slot>
        <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8">
          <div class="h-full border-2 border-gray-200 border-dashed rounded-lg" />
        </div>
      </slot>
      <!-- End main area -->
    </main>
    <aside :class="[hideOnSmallerScreens ? 'hidden xl:flex xl:flex-col' : 'flex flex-col',' relative  shrink-0 w-96 border-l border-gray-200']">
      <!-- Start secondary column (hidden on smaller screens) -->
      <slot name="aside">
        <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8">
          <div class="h-full border-2 border-gray-200 border-dashed rounded-lg" />
        </div>
      </slot>
      <!-- End secondary column -->
    </aside>
  </DarkSidebar>
</template>
<script>
import DarkSidebar from "./DarkSidebar.vue";
export default {
    name: "MultiColumnLayoutSecondaryRight",
    components: {DarkSidebar},
    props: {
        hideOnSmallerScreens: {
            required: false,
            type: Boolean,
            default: () => true
        }
    }
}
</script>

<style scoped>

</style>