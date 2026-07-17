<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <CorporationRecruitment
      v-for="enlistment in enlistments"
      :key="enlistment"
      :enlistment="enlistment"
    />

    <section
      v-if="canManageRecruitment"
      class="space-y-3"
    >
      <div class="px-4 sm:px-0">
        <h2 class="text-sm font-medium text-gray-500">
          Open a corporation for recruitment — pick who can apply
        </h2>
        <p class="mt-1 text-sm text-gray-400">
          Search for a corporation below, then create its Job Posting straight from
          the card: <span class="font-medium">Recruits only</span> lets single
          characters apply, <span class="font-medium">All characters</span> opens it
          to whole accounts.
        </p>
      </div>
      <CorporationList />
    </section>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import CorporationRecruitment from "@/Pages/Corporation/Recruitment/CorporationRecruitment.vue";
import CorporationList from "@/Pages/Corporation/Recruitment/CorporationList.vue";

export default {
    name: "RecruitmentIndex",
    components: {CorporationList, CorporationRecruitment, PageHeader},
    props: {
        canManageRecruitment: {
            required: true,
            type: Boolean
        },
        enlistments: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
          pageTitle: 'Corporation Recruitment',
        }
    }
}
</script>

<style scoped>

</style>
