<template>
  <div class="divide-y divide-gray-200 overflow-y-auto">
    <!-- Owned: the user's own characters/corps, loaded immediately -->
    <section>
      <h3 class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
        {{ isCorporationScope ? 'Your corporations' : 'Your characters' }}
      </h3>
      <ul class="divide-y divide-gray-200">
        <InfiniteLoadingHelper
          v-slot="{results}"
          route-name="manual_job.entities"
          method="POST"
          :params="ownedParams"
        >
          <DispatchableEntry
            v-for="(entity, index) of results"
            :key="`owned ${index}`"
            :entry="entity"
          />
        </InfiniteLoadingHelper>
      </ul>
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
      <ul
        v-if="showAffiliated"
        class="divide-y divide-gray-200"
      >
        <InfiniteLoadingHelper
          v-slot="{results}"
          route-name="manual_job.entities"
          method="POST"
          :params="affiliatedParams"
        >
          <DispatchableEntry
            v-for="(entity, index) of results"
            :key="`affiliated ${index}`"
            :entry="entity"
          />
        </InfiniteLoadingHelper>
      </ul>
    </section>
  </div>
</template>

<script>
import DispatchableEntry from "./DispatchableEntry.vue";
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper.vue";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";

export default {
    name: "DispatchUpdate",
    components: {InfiniteLoadingHelper, DispatchableEntry, ChevronDownIcon},
    data() {
        return {
            showAffiliated: false,
        }
    },
    computed: {
        dispatch_transfer_object() {
            return this.$page.props.dispatch_transfer_object != null ? this.$page.props.dispatch_transfer_object : this.$page.props.dispatchTransferObject
        },
        isCorporationScope() {
            return (_.get(this.dispatch_transfer_object, 'required_corporation_role', []) || []).length > 0
        },
        ownedParams() {
            return {...this.dispatch_transfer_object, ownership: 'owned'}
        },
        affiliatedParams() {
            return {...this.dispatch_transfer_object, ownership: 'affiliated'}
        },
    },
}
</script>
