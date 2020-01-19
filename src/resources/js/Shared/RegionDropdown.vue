<template>
    <div>
        <b-form-select v-model="selected">
            <b-form-select-option :value="null">Please select an option</b-form-select-option>
            <b-form-select-option
                v-for="region in regions"
                :value="region.region_id"
                :key="region.region_id">
                {{region.name}}
            </b-form-select-option>
        </b-form-select>
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
