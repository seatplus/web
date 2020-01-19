<template>
    <div>
        <b-form-select v-model="selected">
            <b-form-select-option :value="null">Please select an option</b-form-select-option>
            <b-form-select-option
                v-for="character in owned"
                :value="character.character_id"
                :key="character.character_id">
                {{character.name}}
            </b-form-select-option>
        </b-form-select>
    </div>
</template>

<script>
    export default {
        name: "CharacterDropdown",
        props: {
            affiliated: {
                type: Array,
                required: false
            },
            owned: {
                type: Array,
                required: true
            },
            characterId: {
                type: Number,
                default: null
            }
        },
        data() {
            return {
                selected: this.characterId
            }
        },
        watch: {
            selected: function () {
                this.$emit('selectedCharacterId', this.selected);
            }
        }
    }
</script>

<style scoped>

</style>
