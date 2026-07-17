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
      <!-- Member list is delivered as a per-corporation page scroll prop
           (MemberTrackingController::index → members_<corporation>), consumed here via
           native Inertia infinite scroll. Replaces the axios/Ziggy useInfinityScrolling loader. -->
      <InfiniteScroll
        :data="scrollKey"
        :items-element="`#${bodyId}`"
        preserve-url
      >
        <StickyHeaderTable
          :header-titles="headerTitles"
          :body-id="bodyId"
        >
          <template #default="{ countColumns, columns }">
            <MemberTrackingListElementForSmallDevices
              v-for="member in members"
              :key="`mobile-${member.character_id}`"
              :member="member"
              :required_scopes="corporation.required_scopes"
            />
            <MemberTrackingListElement
              v-for="member in members"
              :key="`desktop-${member.character_id}`"
              :member="member"
              :required_scopes="corporation.required_scopes"
              :columns="columns"
              :count-columns="countColumns"
            />
          </template>
        </StickyHeaderTable>

        <!-- Shown in whichever trigger is fetching the next/previous page. -->
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

      <!-- Empty state: no member-tracking rows for this corporation yet. -->
      <div
        v-if="members.length === 0"
        class="py-10 text-center text-sm font-medium text-gray-500"
      >
        No member tracking data available.
      </div>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable.vue";
import MemberTrackingListElement from "./MemberTrackingListElement.vue";
import MemberTrackingListElementForSmallDevices from "./MemberTrackingListElementForSmallDevices.vue";
import { InfiniteScroll } from "@inertiajs/vue3";

const headerTitles = [
    {title: 'Token', columnSpan: 1},
    {title: 'Name', columnSpan: 3},
    {title: 'Last Location', columnSpan: 3},
    {title: 'Ship', columnSpan: 3},
    {title: 'Joined', columnSpan: 1},
    {title: 'Last Login', columnSpan: 1},
];

export default {
    name: "MemberTrackingComponent",
    components: {
        InfiniteScroll,
        StickyHeaderTable,
        MemberTrackingListElementForSmallDevices,
        MemberTrackingListElement,
        EntityBlock,
        CardWithHeader,
    },
    props: {
        corporation: {
            required: true,
            type: Object
        },
    },
    setup() {
        return {
            headerTitles,
        }
    },
    computed: {
        // Matches the CorporationMemberTrackingController scroll prop key.
        scrollKey() {
            return `members_${this.corporation.corporation_id}`
        },
        bodyId() {
            return `member-tracking-body-${this.corporation.corporation_id}`
        },
        members() {
            return this.$page.props[this.scrollKey]?.data ?? []
        }
    }
}
</script>

<style scoped>

</style>
