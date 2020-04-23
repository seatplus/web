<template>
    <div>
        <label for="character" class="block text-sm font-medium leading-5 text-gray-700">Character Filter</label>
        <select v-model="selected" id="character" class="mt-1 block form-select w-full py-2 px-3 py-0 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
            <option :value="null">All Characters</option>
            <option v-for="character in owned" :value="character.character_id" :key="character.character_id">
                {{character.name}}
            </option>
        </select>
    </div>

    <!--<div>
        <b-form-select v-model="selected">
            <b-form-select-option :value="null">Please select an option</b-form-select-option>
            <b-form-select-option
                v-for="character in owned"
                :value="character.character_id"
                :key="character.character_id">
                {{character.name}}
            </b-form-select-option>
        </b-form-select>
    </div>-->
</template>

<script>
    export default {
        name: "CharacterDropdown",
        props: {
            characterId: {
                type: Number,
                default: null
            }
        },
        data() {
            return {
                selected: this.characterId,
                owned: this.$page.filters.owned_characters
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
