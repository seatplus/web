<template>
    <Layout page="Corporation" page-description="Member Tracking" :dispatch_transfer_object="dispatch_transfer_object">

        <template v-slot:title>
            <PageHeader>
                Corporation Member Tracking
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Update
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <MemberTrackingComponent v-for="corporation in this.corporations" :corporation="corporation" :key="corporation.corporation_id" />

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
                <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
            </SlideOver>
        </template>
    </Layout>
</template>

<script>
import Layout from "@/Shared/Layout"
import EveImage from "@/Shared/EveImage"
import Time from "@/Shared/Time"
import PageHeader from "@/Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import SlideOver from "@/Shared/Layout/SlideOver"
import DispatchUpdate from "@/Shared/DispatchUpdate"
import MemberTrackingComponent from "./MemberTracking/MemberTrackingComponent";

export default {
    name: "MemberTracking",
    components: {MemberTrackingComponent, DispatchUpdate, SlideOver, HeaderButton, PageHeader, Time, EveImage, Layout},
    props: {
        corporations: {
            type: Array,
            required: true
        },
        dispatch_transfer_object: {
            required: true,
            type: Object
        }
    },
    methods: {
        openSlideOver() {
            this.$eventBus.$emit('open-slideOver', 'update');
        }
    },
}
</script>

<style scoped>

</style>
