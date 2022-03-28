<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      Corporation Recruitment
      <template
        v-if="canManageRecruitment"
        #primary
      >
        <HeaderButton @click="create_enlistment = true">
          Open new enlistment
        </HeaderButton>
      </template>
    </PageHeader>

    <p>Test for something new</p>
    <CorporationRecruitment
      v-for="enlistment in enlistments"
      :key="enlistment"
      :enlistment="enlistment"
    />

    <teleport to="#destination">
      <CreateEnlistmentModal v-model="create_enlistment" />
    </teleport>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import CreateEnlistmentModal from "./CreateEnlistmentModal";
import CorporationRecruitment from "@/Pages/Corporation/Recruitment/CorporationRecruitment";

export default {
    name: "RecruitmentIndex",
    components: {CorporationRecruitment, CreateEnlistmentModal, HeaderButton, PageHeader},
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
