<template>
  <span class="inline-block relative">
    <HeaderButton
      :secondary="true"
      @click="openSlideOver"
    >
      Select {{ type.replace(/^\w/, c => c.toUpperCase()) }}
    </HeaderButton>
    <span
      v-if="has_selected"
      class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-amber-400"
    />
  </span>
  <teleport to="#destination">
    <SlideOver v-model:open="open">
      <template #title>
        Select {{ type }}
      </template>
      <div class="relative overflow-y-auto">
        <div class="z-10 sticky top-0">
          <div class="ml-3 mb-3 relative rounded-md bg-white shadow-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <input
              id="search"
              v-model="search"
              type="search"
              name="search"
              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
              :placeholder="`${type} name`"
            >
          </div>
        </div>
        <EntitySelection
          :dispatch-transfer-object="dispatchTransferObject"
          :type="type"
          :search="search"
        />
      </div>
    </SlideOver>
  </teleport>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import HeaderButton from "../../Layout/HeaderButton.vue";
import SlideOver from "../../Layout/SlideOver.vue";
import EntitySelection from "./EntitySelection.vue";
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    type: {
        type: String,
        default: () => 'character'
    }
});

const page = usePage();

const open = ref(false)
const search = ref('')

const has_selected = computed(() => {
    // The picker writes the current selection into the page URL as `${type}_ids[…]`.
    const params = new URLSearchParams(window.location.search)

    for (const key of params.keys()) {
        if (key.startsWith(`${props.type}_ids`)) {
            return true
        }
    }

    return false
})

const dispatchTransferObject = computed(() => page.props.dispatch_transfer_object != null ? page.props.dispatch_transfer_object : page.props.dispatchTransferObject)

function openSlideOver() {
    open.value = true
}
</script>

<style scoped>

</style>
