<template>
    <Layout page="Character Contacts" :dispatch_transfer_object="dispatch_transfer_object">

        <template v-slot:title>
            <PageHeader>
                Character Contacts
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
            <CharacterContactPanel v-for="character_affiliation in characters" :key="character_affiliation.character_id" :character="character_affiliation.character" :corporation_id="character_affiliation.corporation_id" :alliance_id="character_affiliation.alliance_id" />
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
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
export default {
    name: "Index",
    components: {
        CharacterSelectionButton,
        HeaderButton, PageHeader, ListTransition, CharacterContactPanel, DispatchUpdate, SlideOver, Layout},
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
        characters: {
            required: true,
            type: Array
        }
    },
    methods: {
        openSlideOver(value) {
            //TODO this.$eventBus.$emit('open-slideOver', value);
        },
    }
}
</script>

<style scoped>

</style>
