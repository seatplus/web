<script setup>
import {useToasts} from "@/Functions/useToasts.js";
import {computed, nextTick, onMounted, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import Toast from "@/Shared/Toasts/Toast.vue";

const toasts = useToasts()

const visible = toasts.visibleToasts

const addToast = (text) => {
    toasts.addToast(text)
}

// get flash messages from the server
const flashMessages = computed(() => {
    return usePage().props.flash;
});

// watch for changes in flash messages
// this is needed for the flash messages that are set after the page is loaded (client side rendering)
watch(flashMessages, (flash) => {
    handleFlashMessages(flash)
});

// handle flash messages on mount
// this is needed for the first page load (server side rendering)
onMounted(() => {
    handleFlashMessages(flashMessages.value)

    // remove flash messages from the server
    usePage().props.flash = {}
})

const handleFlashMessages = async (flash) => {
    await nextTick(() => {
        for (const [key, value] of Object.entries(flash)) {
            if(toasts.types.includes(key) && value) {
                addToast(value, {appearance: key})
            }
        }
    })
}

</script>

<template>
  <div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
    <div class="max-w-sm w-full">
      <transition-group
              tag="div"
              :enter-active-class="visible.length > 1 ? 'transform ease-out delay-300 duration-300 transition': 'transform ease-out duration-300 transition'"
              enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
              enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
              leave-active-class="transition ease-in duration-500"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
              move-class="transition ease-in-out duration-500"
      >
        <Toast
                v-for="(toast, idx) in visible"
                :key="toast.id"
                :toast="toast"
                :class="[{'mt-4': idx > 0 }]"
        />
      </transition-group>
    </div>
  </div>
</template>

