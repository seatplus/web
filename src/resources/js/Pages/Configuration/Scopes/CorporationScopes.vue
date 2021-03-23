<template>
    <div>
        <h3 class="text-lg font-medium leading-6 text-gray-900">Corporation Scopes</h3>
        <div v-for="entity in flavours">
            <label  class="inline-flex items-center">
                <input
                    type="checkbox"
                    :value="entity.value"
                    v-model="selected"
                    class="form-checkbox"
                    @change="$emit('corporation-scopes', selected)"
                >
                <span class="ml-2">{{entity.text}}</span>
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CorporationScopes",
        props: {
            scopes: {
                type: Object,
                required: true
            },
            selectedScopes: {
                type    : Array,
                required: false,
                default : []
            }
        },
        data() {
            return {
                selected: []
            }
        },
        computed: {
            flavours: function () {
                return _.map(this.scopes, (value, prop) => ({
                    text: _.capitalize(prop),
                    value: _.toString(value)
                }));
            },
        },
        mounted() {
            /*TODO this.$eventBus.$on('role-removed', () => {
                this.selected = []
                this.$emit('corporation-scopes', this.selected)
            })*/
        },
        watch: {
            selectedScopes() {
                this.selected = this.selectedScopes
            }
        }
    }
</script>

<style scoped>

</style>
