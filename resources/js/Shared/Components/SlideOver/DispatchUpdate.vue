<template>
  <div class="divide-y divide-gray-200 overflow-y-auto">
    <!-- Owned: the user's own characters/corps, loaded immediately -->
    <section>
      <h3 class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
        {{ isCorporationScope ? 'Your corporations' : 'Your characters' }}
      </h3>
      <DispatchEntityList
        :params="ownedParams"
        label="owned"
        :empty-text="isCorporationScope ? 'no corporations you own are eligible' : 'no characters you own are eligible'"
      />
    </section>

    <!-- Affiliated: everything else the user may manage, fetched only when expanded -->
    <section>
      <button
        type="button"
        class="flex w-full items-center justify-between px-6 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 hover:bg-gray-50"
        @click="showAffiliated = !showAffiliated"
      >
        <span>{{ isCorporationScope ? 'Affiliated corporations' : 'Affiliated characters' }}</span>
        <ChevronDownIcon
          class="h-4 w-4 transition-transform"
          :class="{'rotate-180': showAffiliated}"
        />
      </button>
      <DispatchEntityList
        v-if="showAffiliated"
        :params="affiliatedParams"
        label="affiliated"
        empty-text="no affiliated entities"
      />
    </section>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
import DispatchEntityList from "./DispatchEntityList.vue";

const page = usePage();
const showAffiliated = ref(false);

const dispatchTransferObject = computed(
    () => page.props.dispatch_transfer_object ?? page.props.dispatchTransferObject
);
const isCorporationScope = computed(
    () => (dispatchTransferObject.value?.required_corporation_role ?? []).length > 0
);
const ownedParams = computed(() => ({ ...dispatchTransferObject.value, ownership: 'owned' }));
const affiliatedParams = computed(() => ({ ...dispatchTransferObject.value, ownership: 'affiliated' }));
</script>
