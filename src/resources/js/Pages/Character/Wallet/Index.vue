<template>
    <Layout page="Character Wallets" :required-scopes="dispatch_transfer_object.required_scopes">

        <template v-slot:title>
            <PageHeader>
                Character Wallets
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Update
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <div class="space-y-4">
            <WalletComponent :id="character_id" v-for="character_id of character_ids" :key="character_id" />
        </div>

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
                <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
            </SlideOver>
        </template>
    </Layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import SlideOver from "@/Shared/Layout/SlideOver";
import DispatchUpdate from "@/Shared/DispatchUpdate";
import CharacterContactPanel from "@/Shared/Components/CharacterContactPanel";
import ListTransition from "@/Shared/Transitions/ListTransition";
import PageHeader from "@/Shared/Layout/PageHeader";
import HeaderButton from "@/Shared/Layout/HeaderButton";
import WalletComponent from "./WalletComponent";

export default {
    name: "Index",
    components: {
        WalletComponent,
        HeaderButton, PageHeader, ListTransition, CharacterContactPanel, DispatchUpdate, SlideOver, Layout},
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
        character_ids: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            entities: [],
            ready: false
        }
    },
    methods: {
        openSlideOver() {
            this.$eventBus.$emit('open-slideOver');
        },

    },
    created: function () {

    }
}
</script>

<style scoped>

</style>
