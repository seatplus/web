<template>
    <span class="inline-block relative">
        <HeaderButton :secondary="true" @click="openSlideOver('character')">
            Select Characters
        </HeaderButton>
        <span v-if="has_selected" class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-yellow-400"></span>
    </span>
</template>

<script>
import HeaderButton from "@/Shared/Layout/HeaderButton";
export default {
    name: "CharacterSelectionButton",
    components: {HeaderButton},
    methods: {
        openSlideOver(value) {
            this.$eventBus.$emit('open-slideOver', value);
        },
    },
    computed: {
        has_selected() {
            let character_ids = _.get(this.$route().params, 'character_ids')

            return !!character_ids
        }
    }
}
</script>

<style scoped>

</style>
