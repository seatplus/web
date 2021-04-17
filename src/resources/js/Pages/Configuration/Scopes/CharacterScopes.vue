<template>
  <div>
    <h3 class="text-lg font-medium leading-6 text-gray-900">
      Character Scopes
    </h3>
    <div class="space-y-1">
      <ScopeToggle
        v-for="flavour in flavours"
        :key="`${flavour.text}:${scopesAsString}`"
        v-model:selected="selected"
        :scope="flavour"
      />
    </div>
  </div>
</template>

<script>
import ScopeToggle from "./ScopeToggle";
export default {
    name: "CharacterScopes",
    components: {ScopeToggle},
    props: {
        scopes: {
            type: Object,
            required: true
        },
        selectedScopes: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    emits: ['update:selectedScopes'],
    data() {
        return {
            selected: this.selectedScopes
        }
    },
    computed: {
        flavours: function () {
            return _.map(this.scopes, (value, prop) => ({
                text: _.capitalize(prop),
                value: value
            }));
        },
        scopesAsString() {
            return _.toString(this.selectedScopes)
        }
    },
    watch: {
        selected(newValue) {
            this.$emit('update:selectedScopes', newValue)
        }
    }
}
</script>

<style scoped>

</style>
