<template>
  <transition
    leave-active-class="duration-500"
  >
    <div
      v-show="open"
      class="fixed inset-0 overflow-hidden z-20"
    >
      <div class="absolute inset-0 overflow-hidden">
        <transition
          enter-active-class="ease-in-out duration-500"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="ease-in-out duration-500"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="open"
            class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="flipStatus()"
          />
        </transition>

        <section class="absolute inset-y-0 right-0 pl-10 max-w-full flex">
          <transition
            enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
          >
            <div
              v-if="open"
              class="w-screen max-w-md"
            >
              <div class="h-full flex flex-col space-y-6 py-6 bg-white shadow-xl overflow-y-scroll">
                <header class="px-4 sm:px-6">
                  <div class="flex items-start justify-between space-x-3">
                    <h2 class="text-lg leading-7 font-medium text-gray-900">
                      <slot name="title">
                        Panel title
                      </slot>
                    </h2>
                    <div class="h-7 flex items-center">
                      <button
                        aria-label="Close panel"
                        class="text-gray-400 hover:text-gray-500 transition ease-in-out duration-150"
                        @click="flipStatus"
                      >
                        <svg
                          class="h-6 w-6"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
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
                  </div>
                </header>
                <slot>
                  <div class="relative flex-1 px-4 sm:px-6">
                    <!-- Replace with your content -->
                    <div class="absolute inset-0 px-4 sm:px-6">
                      <div class="h-full border-2 border-dashed border-gray-200" />
                    </div>
                    <!-- /End replace -->
                  </div>
                </slot>
              </div>
            </div>
          </transition>
        </section>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "SlideOver",
  props: {
      open: Boolean
  },
  emits: ['update:open'],
  data() {
      return {
          //open: this.value,
      }
  },
  mounted() {
      /*this.$eventBus.$on('open-slideOver', () => this.open = true)*/
  },
  methods: {
      flipStatus() {
          this.$emit('update:open', !this.open)
      }
  },
}
</script>

<style scoped>

</style>
