<template>
    <Layout page="Corporation" page-description="Recruitment">

        <template v-slot:title>
            <PageHeader>
                Corporation Recruitment
                <template v-if="can_manage_recruitment" v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Update
                    </HeaderButton>
                </template>
            </PageHeader>
        </template>

        <span>Test </span>

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
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

export default {
    components: {CorporationList, SlideOver, Layout, HeaderButton, PageHeader},
    name: "RecruitmentIndex",
    props: {
        can_manage_recruitment: {
            required: true,
            type: Boolean
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
