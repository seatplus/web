<template>
  <!-- Static sidebar for desktop -->
  <div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64">
      <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
        <img
          class="h-8 w-auto"
          :src="$page.props.images.logo"
          alt="SeAT plus"
        >
      </div>
      <div class="h-0 flex-1 flex flex-col overflow-y-auto">
        <Sidebar :active-entry-url="getActiveSidebarElement()" />
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "./Sidebar.vue";
export default {
  name: "DesktopSidebar",
  components: {Sidebar},
  props: {
    activeSidebarElement: {
      type: String,
      required: true,
    },
  },
  methods: {
    getActiveSidebarElement() {
      return this.activeSidebarElement !== '' ? this.activeSidebarElement : window.location.href
    }
  }
}
</script>

<style scoped>

</style>
