<template>
    <b-form-group>
        <template v-slot:label>
            <b>Choose {{type}} Scopes:</b><br>
            <b-form-checkbox
                v-model="allSelected"
                :indeterminate="intermediate"
                aria-describedby="flavours"
                aria-controls="flavours"
                @change="toggleAll"
            >
                {{ allSelected ? 'Un-select All Scopes' : 'Select All Scopes' }}
            </b-form-checkbox>
        </template>

        <b-form-checkbox-group
            id="flavours"
            v-model="selected"
            :options="flavours"
            name="flavours"
            class="ml-4"
            aria-label="Individual flavours"
            stacked
        />
    </b-form-group>
</template>

<script>
    export default {
        name: "ScopeCheckbox",
        props: {
            flavours: {
                type: Array,
                required: true
            },
            selectedFlavours: {
                type: Array,
                required: false,
                default: () => { return [] }
            },
            type: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                allSelected: false,
                intermediate: false,
                selected: this.selectedFlavours
            }
        },
        methods: {
            toggleAll(checked) {

                let mappedOptions = _.map(this.flavours.slice(), (flavor) => (flavor.value));

                this.selected = checked ? mappedOptions : []
            },
        },
        watch: {
            selected(newVal, oldVal) {
                // Handle changes in individual flavour checkboxes
                if (newVal.length === 0) {
                    this.intermediate = false
                    this.allSelected = false
                } else if (newVal.length === this.flavours.length) {
                    this.intermediate = false
                    this.allSelected = true
                } else {
                    this.intermediate = true
                    this.allSelected = false
                }

                this.$emit('selected-scopes', this.selected)
            },
        }
    }
</script>

<style scoped>

</style>
