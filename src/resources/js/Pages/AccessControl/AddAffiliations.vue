<template>
    <div class="max-w-md rounded-md relative shadow-sm w-full">
        <!--<input id="price" class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5" placeholder="0.00" />-->
        <SeatPlusSelect v-model="selected">
            <option v-for="entity in availableEntities.characters" :key="entity.name" :value="entity.character_id">
                {{ entity.name }} (Character)
            </option>
            <option v-for="entity in availableEntities.corporations" :key="entity.name" :value="entity.corporation_id">
                {{ entity.name }} (Corporation)
            </option>
            <option v-for="entity in availableEntities.alliances" :key="entity.name" :value="entity.alliance_id">
                {{ entity.name }} (Alliance)
            </option>
        </SeatPlusSelect>
        <!--TODO: create view for mobile-->
        <div class="absolute inset-y-0 right-0 flex items-center">
            <select v-model="type" id="country" class="form-select h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm sm:leading-5">
                <option value="allowed">Allowed</option>
                <option value="inverse">Inverse</option>
                <option value="forbidden">Forbidden</option>
            </select>
        </div>
        <div class="flex items-center">
            <span class="inline-flex rounded-md shadow-sm">
                <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs leading-4 font-medium rounded text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                    Button text
                </button>
            </span>
        </div>
    </div>
</template>

<script>
    import SeatPlusSelect from "../../Shared/SeatPlusSelect"
    export default {
        name: "AddAffiliations",
        components: {SeatPlusSelect},
        props: ['availableEntities'],
        data() {
            return {
                type: 'allowed',
                selected: 0
            }
        }
    }
</script>

<style scoped>

</style>
