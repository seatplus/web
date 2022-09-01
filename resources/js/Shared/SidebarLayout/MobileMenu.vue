<template>
  <!-- Off-canvas menu for mobile -->
  <transition leave-active-class="duration-300">
    <div
      class="md:hidden"
    >
      <div class="fixed inset-0 flex z-40">
        <transition
          enter-active-class="transition-opacity ease-linear duration-300"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity ease-linear duration-300"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="sidebarOpen"
            class="fixed inset-0"
            @click="closeSidebar"
          >
            <div class="absolute inset-0 bg-gray-600 opacity-75" />
          </div>
        </transition>
        <transition
          enter-active-class="transition ease-in-out duration-300 transform"
          enter-from-class="-translate-x-full"
          enter-to-class="translate-x-0"
          leave-active-class="transition ease-in-out duration-300 transform"
          leave-from-class="translate-x-0"
          leave-to-class="-translate-x-full"
        >
          <div
            v-show="sidebarOpen"
            class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gray-800"
          >
            <div class="absolute top-0 right-0 -mr-14 p-1">
              <button
                v-show="sidebarOpen"
                class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600"
                aria-label="Close sidebar"
                @click="closeSidebar"
              >
                <svg
                  class="h-6 w-6 text-white"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
            <div class="shrink-0 flex items-center px-4">
              <img
                class="h-8 w-auto"
                :src="$page.props.images.logo"
                alt="SeAT plus"
              >
            </div>
            <div class="mt-5 flex-1 h-0 overflow-y-auto">
              <Sidebar :active-entry-url="getActiveSidebarElement()" />
            </div>
          </div>
        </transition>
        <div class="shrink-0 w-14">
          <!-- Dummy element to force sidebar to shrink to fit close icon -->
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import Sidebar from "./Sidebar.vue";
export default {
  name: "MobileMenu",
  components: {Sidebar},
  props: {
    sidebarOpen: {
      type: Boolean,
      default: false
    },
    activeSidebarElement: {
      type: String,
      required: true,
    },
  },
  emits: ['update:modelValue'],
  methods: {
    getActiveSidebarElement() {
      return this.activeSidebarElement !== '' ? this.activeSidebarElement : window.location.href
    },
    closeSidebar() {
      this.$emit('update:sidebarOpen', false)
    }
  }
}
</script>

<style scoped>

</style>
