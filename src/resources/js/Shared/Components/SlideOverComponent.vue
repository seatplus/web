<template>
    <div>

        <SlideOver v-model="update_open">
            <template v-slot:title>Dispatch Update Job</template>
            <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
        </SlideOver>

        <SlideOver v-model="select_character_open">
            <template v-slot:title>Select characters</template>
            <CharacterSelection :dispatch_transfer_object="dispatch_transfer_object" />
        </SlideOver>

    </div>

</template>

<script>
import SlideOver from "@/Shared/Layout/SlideOver";
import DispatchUpdate from "@/Shared/DispatchUpdate";
import CharacterSelection from "@/Shared/Components/SlideOver/CharacterSelection";
export default {
    name: "SlideOverComponent",
    components: {CharacterSelection, DispatchUpdate, SlideOver},
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
    },
    data: () => ({
        update_open: false,
        select_character_open: false,
    }),
    mounted() {
        this.$eventBus.$on('open-slideOver', (payload) => {
            if(payload === 'update')
                this.update_open = true

            if(payload === 'character')
                this.select_character_open = true

        })
    }
}
</script>

<style scoped>

</style>
