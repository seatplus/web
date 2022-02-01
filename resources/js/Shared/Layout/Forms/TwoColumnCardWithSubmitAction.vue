<template>
  <div>
    <div :class="{'mt-10 sm:mt-0' : index>0}">
      <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              <slot name="title">
                Title
              </slot>
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              <slot name="description">
                Description
              </slot>
            </p>
          </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
          <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
              <slot />
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <slot name="button">
                <!--                                <button @click="submit" :disabled="form.processing" type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save
                                </button>-->
              </slot>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--     border -->
    <div
      v-show="withBottomBorder"
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
    name: "TwoColumnCardWithSubmitAction",
    props: {
        index: {
            required: false,
            type: Number,
            default: 0
        },
        withBottomBorder: {
            required: false,
            type: Boolean,
            default: true
        }
    },
}
</script>

<style scoped>

</style>
