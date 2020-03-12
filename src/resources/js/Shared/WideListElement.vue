<template>
    <li>
        <inertia-link :href="this.url" class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
            <div class="flex items-center px-4 py-4 sm:px-6">
                <div class="min-w-0 flex-1 flex items-center">
                    <div class="flex overflow-x-visible">
                        <slot name="avatar"/>
                    </div>
                    <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                        <div>
                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                <slot name="upper_left" />
                            </div>
                            <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                <slot name="lower_left"/>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div>
                                <div class="text-sm leading-5 text-gray-900">
                                    <slot name="upper_right" />
                                </div>
                                <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                    <slot name="lower_right" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <slot name="navigaton" />
                </div>
            </div>
        </inertia-link>
    </li>
</template>

<script>
  export default {
    name: "WideListElement",
      props: {
          url: {
              type    : String,
              required: true
          },
      },
  }
</script>

<style scoped>

</style>
