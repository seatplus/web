<template>
  <slot />
  <div
    v-show="isComplete"
    class="relative block w-full text-center"
  >
    <span
      v-if="noResults"
      class="block text-sm font-medium text-gray-900"
    >
      no entries loaded
    </span>
    <span
      v-else
      class="block text-sm font-medium text-gray-900"
    >
      no more entries
    </span>
  </div>
  <TransitionRoot
    :show="showLoadingIndicator"
    appear
    enter="transition-opacity duration-75"
    enter-from="opacity-0"
    enter-to="opacity-100"
    leave="transition-opacity duration-150"
    leave-from="opacity-100"
    leave-to="opacity-0"
  >
    <div
      class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      <svg
        class="animate-spin mx-auto h-12 w-12 text-gray-400"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
      <span class="mt-2 block text-sm font-medium text-gray-900">
        loading resource
      </span>
    </div>
  </TransitionRoot>

  <div ref="scrollComponent" />
</template>

<script>
import { useInfinityScrolling } from "@/Functions/useInfinityScrolling";
import { TransitionRoot } from '@headlessui/vue'
import {computed, onMounted} from "vue";

export default {
    name: "InfiniteLoadingHelper",
    components: {
        TransitionRoot,
    },
    props: {
        key: {
            type: Number,
            default: () => +new Date()
        },
        route: {
            type: String,
            required: true
        },
        params: {
            required: false,
            type: Object,
            default: () => new Object()
        }
    },
    emits: ['result'],
    setup(props, { emit }) {

        const results = useInfinityScrolling(props.route, props.params);

        const noResults = computed(() => results.isComplete.value && results.result.value.length === 0)
        const isComplete = computed(() => results.isComplete.value)
        const showLoadingIndicator = computed(() => results.isLoading.value)

        onMounted(() => {
            emit('result', results.result.value)
        })

        return {
            results,
            noResults,
            isComplete,
            scrollComponent: results.scrollComponent,
            showLoadingIndicator
        }
    }
}
</script>

<style scoped>

</style>