<template>
  <!-- scroll-region: lets Inertia track/restore this custom scroll container's
       position so <InfiniteScroll> merges the next page without jumping to top. -->
  <div
    class="relative max-h-96 overflow-y-auto"
    scroll-region=""
  >
    <InfiniteScroll
      :key="scrollKey"
      :data="scrollKey"
      :items-element="`#${bodyId}`"
      preserve-url
    >
      <ApplicationsTable
        :applications="applications"
        :body-id="bodyId"
      />
    </InfiniteScroll>
  </div>
</template>

<script>
import { InfiniteScroll } from "@inertiajs/vue3";
import ApplicationsTable from "@/Pages/Corporation/Recruitment/ApplicationsTable/ApplicationsTable.vue";

export default {
    name: "PendingTable",
    components: {
        ApplicationsTable,
        InfiniteScroll,
    },
    props: {
        stepCount: {
            required: true,
            type: Number
        },
        corporationId: {
            required: true,
            type: Number
        }
    },
    computed: {
        // Open applications are delivered as a page scroll prop keyed per corporation +
        // review step (GetRecruitmentIndexController), consumed via <InfiniteScroll :data>.
        scrollKey() {
            return `open_${this.corporationId}_${this.stepCount}`
        },
        bodyId() {
            return `open-body-${this.corporationId}-${this.stepCount}`
        },
        applications() {
            return this.$page.props[this.scrollKey]?.data ?? []
        }
    }
}
</script>

<style scoped>

</style>
