<script setup>
import { CheckCircleIcon, InformationCircleIcon, ExclamationCircleIcon, XCircleIcon} from '@heroicons/vue/24/outline'
import { XMarkIcon } from '@heroicons/vue/20/solid'
import {ref} from "vue";
import {useToasts} from "@/Functions/useToasts.js";

const props = defineProps({
    toast: {
        type: Object,
        required: true,
        validator(value) {
            // check if the object has the required properties: appearance, message
            return value.hasOwnProperty('appearance') && value.hasOwnProperty('message')

        }
    },
})

const remove = () => {
    useToasts().removeToast(props.toast.id)
}

const isInfo = ref(props.toast.appearance === 'info')
const isSuccess = ref(props.toast.appearance === 'success')
const isWarning = ref(props.toast.appearance === 'warning')
const isError = ref(props.toast.appearance === 'error')

</script>

<template>
  <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
    <div class="p-4">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <CheckCircleIcon v-if="isSuccess" class="h-6 w-6 text-green-400" aria-hidden="true" />
          <InformationCircleIcon v-if="isInfo" class="h-6 w-6 text-blue-400" aria-hidden="true" />
          <ExclamationCircleIcon v-if="isWarning" class="h-6 w-6 text-amber-400" aria-hidden="true" />
          <XCircleIcon v-if="isError" class="h-6 w-6 text-red-400" aria-hidden="true" />
        </div>
        <div class="ml-3 w-0 flex-1 pt-0.5">
          <p class="text-sm font-medium text-gray-900">{{ $I18n.trans(`web::notifications.${toast.appearance}`) }}</p>
          <p class="mt-1 text-sm text-gray-500">{{ toast.message }}</p>
        </div>
        <div class="ml-4 flex flex-shrink-0">
          <button type="button" @click="remove" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <span class="sr-only">Close</span>
            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>