<template>
    <span :class="trackClass" class="text-left relative inline-block flex-no-shrink h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline" role="checkbox" tabindex="0" @click="toggle()" @keydown.space.prevent="toggle()" :aria-checked="value.toString()">
        <span aria-hidden="true" :class="{ 'translate-x-5': value, 'translate-x-0': !value }" class="inline-block h-5 w-5 rounded-full bg-white shadow transform transition ease-in-out duration-200"></span>
    </span>
</template>

<script>
  export default {
    name: "SimpleToggle",
      props: {
          bgClass: {
              type: String,
              default: 'bg-indigo-600'
          },
          value: {
              type: Boolean,
              default: false
          }
      },
      computed: {
          trackClass() {
              return this.value ? this.bgClass : 'bg-gray-200';
          }
      },
      data() {
          return {
              focused: false /*This Needed?*/
          };
      },
      methods: {
          toggle() {
              this.$emit('change', !this.value);
          }
      }
  }
</script>

<style scoped>

</style>
