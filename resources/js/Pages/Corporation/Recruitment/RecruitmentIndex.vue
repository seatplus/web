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
import CorporationRecruitment from "./CorporationRecruitment"
import CreateEnlistmentModal from "./CreateEnlistmentModal";

export default {
    name: "RecruitmentIndex",
    components: {CreateEnlistmentModal, CorporationRecruitment, HeaderButton, PageHeader},
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
