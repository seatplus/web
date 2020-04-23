<template>
    <div>
        <label for="region" class="block text-sm font-medium leading-5 text-gray-700">Region Filter</label>
        <select v-model="selected" id="region" class="mt-1 block form-select w-full py-2 px-3 py-0 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
            <option :value="null">All Regions</option>
            <option v-for="region in regions" :value="region.region_id" :key="region.region_id">
                {{region.name}}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "RegionDropdown",
        props: {
            regions: {
                type: Array,
                required: true
            },
            regionId: {
                type: Number,
                default: null
            }
        },
        data() {
            return {
                selected: this.regionId
            }
        },
        watch: {
            selected: function () {
                this.$emit('selectedRegionId', this.selected);
            }
        }
    }
</script>

<style scoped>

</style>
