<template>
  <div>
    <h3 class="text-lg font-medium leading-6 text-gray-900">
      Corporation Scopes
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
import ScopeToggle from "./ScopeToggle.vue";
import {computed, ref, watch} from "vue";
export default {
    name: "CorporationScopes",
    components: {ScopeToggle},
    props: {
        scopes: {
            type: Object,
            required: true
        },
        selectedScopes: {
            type    : Array,
            required: false,
            default: () => []
        }
    },
    emits: ['update:selectedScopes'],
    setup(props, { emit }) {

        const selected = ref(filter())
        const scopes= ref(_.toArray(props.scopes))

        function filter() {

            if(!props.selectedScopes.includes('esi-characters.read_corporation_roles.v1')) {

                emit('update:selectedScopes', _.pullAll(props.selectedScopes, _.flatten(_.toArray(props.scopes))))
            }

            return props.selectedScopes
        }

        const hasCorporationScopesSelected = computed( () =>  {
            return _.chain(scopes.value)
                .toArray()
                .flatten()
                .intersection(selected.value)
                .value().length > 0
        })

        watch(selected, (newValue) => {

            // add corporation division if wallet or assets is selected
            if(_.intersection(['esi-wallet.read_corporation_wallets.v1', 'esi-assets.read_corporation_assets.v1'], newValue).length > 0)
                newValue.push('esi-corporations.read_divisions.v1')

            // if any corporation scope is selected and role is not yet selected, push role scope
            if(hasCorporationScopesSelected.value && !newValue.includes('esi-characters.read_corporation_roles.v1'))
                newValue.push('esi-characters.read_corporation_roles.v1')

            emit('update:selectedScopes', _.uniq(newValue))
        })


        return {
            selected
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
        },
    },
}
</script>

<style scoped>

</style>
