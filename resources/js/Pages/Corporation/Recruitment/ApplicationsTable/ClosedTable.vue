<template>
  <!-- scroll-region: lets Inertia track/restore this custom scroll container's
       position so <InfiniteScroll> merges the next page without jumping to top. -->
  <div
    class="relative max-h-96 overflow-y-auto"
    scroll-region=""
  >
    <InfiniteScroll
      :data="scrollKey"
      :items-element="`#${bodyId}`"
      preserve-url
    >
      <ApplicationsTable
        :applications="applications"
        :body-id="bodyId"
      >
        <template #default="{ applicant }">
          <ActivityLogModal :application-id="applicant.application_id" />
        </template>
      </ApplicationsTable>
    </InfiniteScroll>
  </div>
</template>

<script>
import { InfiniteScroll } from "@inertiajs/vue3";
import ApplicationsTable from "./ApplicationsTable.vue";
import ActivityLogModal from "./ActivityLogModal.vue";

export default {
    name: "ClosedTable",
    components: {
        ActivityLogModal,
        ApplicationsTable,
        InfiniteScroll,
    },
    props: {
        corporationId: {
            required: true,
            type: Number
        }
    },
    computed: {
        // Closed applications are delivered as a page scroll prop keyed per corporation
        // (GetRecruitmentIndexController), consumed via <InfiniteScroll :data>.
        scrollKey() {
            return `closed_${this.corporationId}`
        },
        bodyId() {
            return `closed-body-${this.corporationId}`
        },
        applications() {
            return this.$page.props[this.scrollKey]?.data ?? []
        }
    }
}
</script>

<style scoped>

</style>
