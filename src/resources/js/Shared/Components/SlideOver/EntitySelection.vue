<template>
  <ul
    ref="scrollComponent"
    class="relative z-0 divide-y divide-gray-200 overflow-y-auto border-t"
  >
    <InfiniteLoadingHelper
      :key="Object.values(params).join(',')"
      :route="route"
      :params="params"
      @result="(results) => entities = results"
    >
      <SelectionEntity
        v-for="character in entities"
        :key="character.character_id"
        v-model="selected_ids"
        :entity="character"
      />
    </InfiniteLoadingHelper>
  </ul>
  <div ref="scrollComponent" />
</template>

<script>
import SelectionEntity from "./SelectionEntity";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import {computed, ref, watch} from "vue";

export default {
    name: "EntitySelection",
    components: {InfiniteLoadingHelper, SelectionEntity},
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object
        },
        type: {
            type: String,
            default: () => 'character'
        },
        search: {
            type: String,
            default: ''
        }
    },
    setup(props) {

        const entities = ref([])
        const params = ref(props.type === 'character'
            ? { permission: props.dispatchTransferObject.permission }
            : { permission: props.dispatchTransferObject.permission, corporation_role: props.dispatchTransferObject.required_corporation_role}
        )

        const search = computed(() => props.search)
        const route = computed( () => props.type === 'character' ? 'get.affiliated.characters' : 'get.affiliated.corporations')

        watch(search,(newValue) => {
            newValue.length >= 3 ? params.value.search = newValue : delete params.value.search
        })

        return {
            route,
            params,
            entities
        }
    },
    data() {
        return {
            initial_ids: [],
            selected_ids: []
        }
    },
    computed: {
        changed() {
            return !_.isEqual(this.initial_ids, this.selected_character_ids)
        }
    },
    beforeMount() {

        let ids = _.get(this.$route().params, `${this.type}_ids`)

        if(!ids)
            return

        this.selected_ids = _.map(ids, (id) => parseInt(id))
        this.initial_ids = _.map(ids, (id) => parseInt(id))

    },
    beforeUnmount() {

        if(!this.changed)
            return

        let route = this.$route().current()

        if(_.isEmpty(this.selected_ids))
            return this.$inertia.get(this.$route(route))

        let queryParameter = this.type === 'character' ? { character_ids: this.selected_ids } : {corporation_ids: this.selected_ids}

        this.$inertia.get(this.$route(route, {_query: queryParameter}))
    },
}
</script>

<style scoped>

</style>
