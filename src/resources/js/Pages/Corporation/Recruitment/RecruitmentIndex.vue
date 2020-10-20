<template>
    <Layout page="Corporation" page-description="Recruitment">

        <template v-slot:title>
            <PageHeader>
                Corporation Recruitment
                <template v-if="can_manage_recruitment" v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Open new enlistment
                    </HeaderButton>
                </template>
            </PageHeader>
        </template>

        <CorporationRecruitment v-for="corporation in corporations" :key="corporation.corporation_id" :corporation="corporation"></CorporationRecruitment>

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Create Enlistment</template>
                <CorporationList></CorporationList>
            </SlideOver>
        </template>

    </Layout>

</template>

<script>
import Layout from "@/Shared/Layout"
import PageHeader from "@/Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import SlideOver from "@/Shared/Layout/SlideOver"
import CorporationList from "./CorporationList"
import CorporationRecruitment from "./CorporationRecruitment"

export default {
    components: {CorporationRecruitment, CorporationList, SlideOver, Layout, HeaderButton, PageHeader},
    name: "RecruitmentIndex",
    props: {
        can_manage_recruitment: {
            required: true,
            type: Boolean
        },
        corporations: {
            required: false
        }
    },
    methods   : {
        openSlideOver() {
            this.$eventBus.$emit('open-slideOver');
        }
    }
}
</script>

<style scoped>

</style>
