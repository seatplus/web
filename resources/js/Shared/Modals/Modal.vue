<template>
  <div>
    <!--Background overlay, show/hide based on modal state.-->
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed bottom-0 z-10 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center"
      >
        <!--
          -z-10 keeps the backdrop below the panel. The panel used to rely on the `transform`
          utility for its stacking context, but Tailwind v4 dropped the standalone `transform`
          class, so the panel became a non-positioned in-flow box and this fixed-position backdrop
          painted on top of it. Pushing the backdrop to a negative z-index (within this modal's own
          z-10 stacking context) restores backdrop-behind-panel without depending on the panel's
          own classes.
        -->
        <div class="fixed inset-0 transition-opacity -z-10">
          <div
            class="absolute inset-0 bg-gray-500 opacity-75"
            @click="toggle"
          />
        </div>

        <!--Modal panel, show/hide based on modal state.-->
        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <slot />
        </transition>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const open = ref(props.modelValue)

watch(() => props.modelValue, (newVal) => {
    open.value = newVal
})

watch(open, (newVal) => {
    emit('update:modelValue', newVal)
})

function toggle() {
    open.value = !open.value
}
</script>

<style scoped>

</style>
