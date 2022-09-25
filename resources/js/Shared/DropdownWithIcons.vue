<template>
  <div class="relative inline-block text-left">
    <!--@keydown.escape="open = false" @click.away="open = false" -->
    <div>
      <span class="rounded-md shadow-sm">
        <button
          ref="button"
          type="button"
          class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150"
          @click="toggleDropdown"
        >
          Options
          <svg
            class="-mr-1 ml-2 h-5 w-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </span>
    </div>
    <div
      v-if="open"
      class="fixed inset-0 transition-opacity"
    >
      <div
        class="absolute inset-0 bg-transparent"
        @click="toggleDropdown"
      />
    </div>
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      xleave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="open"
        ref="dropdown"
        class="origin-top-right absolute z-10 rotate-0 right-0 mt-2 w-56 rounded-md shadow-lg "
      >
        <slot />
      </div>
    </transition>
  </div>
</template>

<script>
    export default {
        name: "DropdownWithIcons",
        props: {
            index: {
                required: false
            }
        },
        data() {
            return {
                open: false
            }
        },
        methods: {
            toggleDropdown() {
                this.open = ! this.open;

                this.$nextTick(() => {

                    let payload = {
                        'index'       : this.index,
                        'dropdownHeight': this.open ? this.$refs.dropdown.clientHeight : 0
                    }

                    this.$emit('change',  payload)
                })

            },
        },
    }
</script>

<style scoped>

</style>
