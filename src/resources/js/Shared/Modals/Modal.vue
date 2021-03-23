<template>
  <div>
    <!--Background overlay, show/hide based on modal state.-->
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed bottom-0 z-10 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center"
      >
        <div class="fixed inset-0 transition-opacity">
          <div
            class="absolute inset-0 bg-gray-500 opacity-75"
            @click="toggle"
          />
        </div>

        <!--Modal panel, show/hide based on modal state.-->
        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
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
      props: ['modelValue'],
emits: ['update:modelValue'],
      data() {
          return {
              open: this.modelValue
          }
      },
      watch: {
        modelValue(newVal) {
              this.open = newVal
          },
          open(newVal) {
              this.$emit('update:modelValue', newVal)
          }
      },
      methods: {
          toggle() {
              this.open = !this.open
          }
      }
  }
</script>

<style scoped>

</style>
