<template>
  <Modal v-model="open">
    <div
      v-if="open"
      class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full"
    >
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
          <slot name="symbol">
            <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg
                class="h-6 w-6 text-red-600"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>
          </slot>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              <slot name="title">
                Deactivate account
              </slot>
            </h3>
            <div class="mt-2">
              <p class="text-sm leading-5 text-gray-500">
                <slot name="description">
                  Are you sure you want to deactivate your account? All of your data will be permanantly removed. This action cannot be undone.
                </slot>
              </p>
            </div>
          </div>
        </div>
      </div>
      <!--Footer-->
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <slot name="buttons">
          <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
            <button
              type="button"
              class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
              @click="toggle"
            >
              Deactivate
            </button>
          </span>
        </slot>
        <span
          v-if="cancelButton"
          class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"
        >
          <button
            type="button"
            class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring transition ease-in-out duration-150 sm:text-sm sm:leading-5"
            @click="toggle"
          >
            Cancel
          </button>
        </span>
      </div>
    </div>
  </Modal>
</template>

<script>
    import Modal from "./Modal.vue"
    export default {
        name: "ModalWithFooter",
        components: {Modal},
        props: {
            modelValue: {
              required: true
            },
            cancelButton: {
                type: Boolean,
                default: true
            }
        },
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
