<template>
  <div>
    <transition-group
      :enter-active-class="entries.length > 1 ? 'transform ease-out delay-300 duration-300 transition': 'transform ease-out duration-300 transition'"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-500"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
      move-class="transition ease-in-out duration-500"
    >
      <slot />
    </transition-group>
  </div>
</template>

<script>
  export default {
      name: "ListTransition",
      props: {
          entries: {
              default: () => []
          }
      }
  }
</script>

<style scoped>

</style>
