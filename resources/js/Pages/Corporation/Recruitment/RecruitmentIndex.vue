<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle">
      <template
        v-if="canManageRecruitment"
        #primary
      >
        <HeaderButton @click="create_enlistment = true">
          Open new enlistment
        </HeaderButton>
      </template>
    </PageHeader>

    <CorporationRecruitment
      v-for="enlistment in enlistments"
      :key="enlistment"
      :enlistment="enlistment"
    />

    <section
      v-if="canManageRecruitment"
      class="space-y-3"
    >
      <h2 class="px-4 text-sm font-medium text-gray-500 sm:px-0">
        Open a corporation for recruitment
      </h2>
      <CorporationList />
    </section>

    <teleport to="#destination">
      <CreateEnlistmentModal v-model="create_enlistment" />
    </teleport>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import HeaderButton from "@/Shared/Layout/HeaderButton.vue"
import CreateEnlistmentModal from "./CreateEnlistmentModal.vue";
import CorporationRecruitment from "@/Pages/Corporation/Recruitment/CorporationRecruitment.vue";
import CorporationList from "@/Pages/Corporation/Recruitment/CorporationList.vue";

export default {
    name: "RecruitmentIndex",
    components: {CorporationList, CorporationRecruitment, CreateEnlistmentModal, HeaderButton, PageHeader},
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
          create_enlistment: false
        }
    }
}
</script>

<style scoped>

</style>
