<template>
  <div v-if="!is_complete"
      class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
  <slot v-if="is_complete" :data="data" />
</template>

<script setup>

import {onMounted, onUnmounted, ref} from "vue";
import { router, usePage } from '@inertiajs/vue3'

const data = ref([])
const is_complete = ref(false)

const props = defineProps({
  href: {
    type: String,
    // set default value to be current route
    default: () => window.location.href
  },
  method: {
    type: String,
    default: 'GET'
  },
  only: {
    type: String,
    default: 'data'
  }
})

const initialUrl = window.location.href
const next_page_url = ref(initialUrl)
const cancelToken = ref(null)

function loadMore() {
  if (is_complete.value) {
    return
  }

  // return if next page url is not set
  if (!next_page_url.value) {
    return
  }

  router.visit(next_page_url.value, {
    method: props.method,
    preserveState: true,
    preserveScroll: true,
    only: [props.only],
    onCancelToken: (token) => {
      cancelToken.value = token
    },
    onSuccess: (page) => {
      data.value = data.value.concat(page.props[props.only].data)
      window.history.replaceState({}, usePage().props.title, initialUrl)
      next_page_url.value = page.props[props.only].next_page_url
      is_complete.value = !next_page_url.value

      loadMore()
    }
  })
}

onMounted(() => {
  loadMore()
})

onUnmounted(() => {
  if (cancelToken.value) {
    cancelToken.value.cancel()
  }
})

</script>