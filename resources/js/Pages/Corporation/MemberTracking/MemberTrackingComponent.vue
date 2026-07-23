<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="grow"
          :entity="corporation"
        />
      </div>
    </template>

    <!-- scroll-region: lets Inertia track/restore this custom scroll container's
         position, so <InfiniteScroll> merges the next page without jumping to top. -->
    <div
      class="relative max-h-96 overflow-y-auto"
      scroll-region=""
    >
      <div class="hidden sm:grid grid-cols-12 gap-x-0 gap-y-1 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-3 py-1 col-span-4">
          Name
        </div>
        <div class="px-3 py-1 col-span-3">
          Last Location
        </div>
        <div class="px-3 py-1 col-span-3">
          Ship
        </div>
        <div class="px-3 py-1">
          Joined
        </div>
        <div class="px-3 py-1">
          Last Login
        </div>
      </div>

      <!-- Native Inertia v3 infinite scroll over the page-level `members_<corporation>`
           scroll prop (MemberTrackingController::index), replacing the axios/Ziggy
           useInfinityScrolling loader. -->
      <InfiniteScroll
        :data="scrollKey"
        :items-element="`#${scrollBodyId}`"
        preserve-url
      >
        <ul
          :id="scrollBodyId"
          class="relative z-0"
        >
          <MemberTrackingListElementForSmallDevices
            v-for="member in members"
            :key="`mobile-${member.character_id}`"
            :member="member"
          />
          <MemberTrackingListElement
            v-for="(member, index) in members"
            :key="member.character_id"
            :member="member"
            :even="index % 2"
          />
        </ul>

        <template #loading>
          <div class="relative block w-full py-6 text-center">
            <svg
              class="animate-spin mx-auto h-8 w-8 text-gray-400"
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
            <span class="mt-2 block text-sm font-medium text-gray-500">
              loading more members…
            </span>
          </div>
        </template>
      </InfiniteScroll>
    </div>
  </CardWithHeader>
</template>

<script setup>
import { computed } from "vue";
import { InfiniteScroll, usePage } from "@inertiajs/vue3";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import MemberTrackingListElement from "./MemberTrackingListElement.vue";
import MemberTrackingListElementForSmallDevices from "./MemberTrackingListElementForSmallDevices.vue";

const props = defineProps({
    corporation: {
        required: true,
        type: Object
    },
});

const page = usePage();

// The members list is delivered as a page scroll prop keyed per corporation
// (MemberTrackingController::index) and consumed by <InfiniteScroll :data="scrollKey">.
const scrollKey = computed(() => `members_${props.corporation.corporation_id}`)

const scrollBodyId = computed(() => `members-body-${props.corporation.corporation_id}`)

const members = computed(() => page.props[scrollKey.value]?.data ?? [])
</script>

<style scoped>

</style>
