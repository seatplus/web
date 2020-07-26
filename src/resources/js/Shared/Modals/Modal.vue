<template>
    <div>
        <!--Background overlay, show/hide based on modal state.-->
        <transition
            enter-active-class="ease-out duration-300"
            enter-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="fixed z-50 bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center">
                <div class="fixed inset-0 transition-opacity">
                    <div @click="toggle" class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!--Modal panel, show/hide based on modal state.-->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <slot />
                </transition>
            </div>
        </transition>
    </div>
</template>

<script>
  export default {
      name: "Modal",
      props: ['value'],
      data() {
          return {
              open: this.value
          }
      },
      methods: {
          toggle() {
              this.open = !this.open
          }
      },
      watch: {
          value(newVal) {
              this.open = newVal
          },
          open(newVal) {
              this.$emit('input', newVal)
          }
      }
  }
</script>

<style scoped>

</style>
