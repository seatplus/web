<template>
    <div ref="menu" class="z-10">
        <div>
            <button @click="toggleMenu" class="max-w-xs flex items-center text-sm rounded-full focus:outline-none focus:shadow-outline">
                <eve-image tailwind_class="h-8 w-8 rounded-full" :object="$page.props.user.data.main_character" :size="256" />
            </button>
        </div>
        <div v-if="menuOpen"  class="fixed inset-0 transition-opacity">
            <div @click="toggleMenu" class="absolute inset-0 bg-transparent"></div>
        </div>
        <transition
            enter-active-class="duration-150 ease-out"
            enter-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-100 ease-in"
            leave-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-show="menuOpen" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                <div class="py-1 rounded-md bg-white shadow-xs">
                    <inertia-link :href="$route('user.settings')" :class="'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150'"> Settings </inertia-link>
                    <inertia-link :href="$route('auth.logout')" :class="'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150'">Sign out</inertia-link>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
  import EveImage from "./EveImage"
  export default {
    name: "Menu",
      components: {EveImage},
      data() {
          return {
              menuOpen: false
          }
      },
      methods: {
          toggleMenu() {
              this.menuOpen = ! this.menuOpen;
          },
      },
  }
</script>

<style scoped>

</style>
