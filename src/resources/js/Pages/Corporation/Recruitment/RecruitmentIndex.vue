<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      Corporation Recruitment
      <template
        v-if="can_manage_recruitment"
        #primary
      >
        <HeaderButton @click="create_enlistment = true">
          Open new enlistment
        </HeaderButton>
      </template>
    </PageHeader>

    <CorporationRecruitment
      v-for="corporation in corporations"
      :key="corporation.corporation_id"
      :corporation="corporation"
    />

    <teleport to="#destination">
      <CreateEnlistmentModal v-model="create_enlistment" />
      <!--<SlideOver v-model:open="create_enlistment">
        <template #title>
          Create Enlistment
        </template>
        <CorporationList :parameters="{permission: 'can open or close corporations for recruitment'}" />
      </SlideOver>-->
    </teleport>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import CorporationRecruitment from "./CorporationRecruitment"
import SlideOver from "@/Shared/Layout/SlideOver";
import CorporationList from "./CorporationList";
import CreateEnlistmentModal from "@/Pages/Corporation/Recruitment/CreateEnlistmentModal";

export default {
    name: "RecruitmentIndex",
    components: {CreateEnlistmentModal, CorporationList, SlideOver, CorporationRecruitment, HeaderButton, PageHeader},
    props: {
        can_manage_recruitment: {
            required: true,
            type: Boolean
        },
        corporations: {
            required: false
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
