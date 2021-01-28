<template>
    <Layout page="Character Wallets" :dispatch_transfer_object="dispatch_transfer_object">

        <template v-slot:title>
            <PageHeader>
                Character Wallets
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver('update')">
                        Update
                    </HeaderButton>
                </template>
                <template v-slot:secondary>
                    <CharacterSelectionButton />
                </template>

            </PageHeader>
        </template>

        <div class="space-y-4">
            <WalletComponent :id="character_id" v-for="character_id of character_ids" :key="character_id" />
        </div>

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
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";

export default {
    name: "Index",
    components: {
        CharacterSelectionButton,
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
        openSlideOver(value) {
            this.$eventBus.$emit('open-slideOver', value);
        },
    },
    created: function () {

    }
}
</script>

<style scoped>

</style>
