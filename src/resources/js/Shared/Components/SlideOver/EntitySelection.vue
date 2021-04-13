<template>
  <ul
    ref="scrollComponent"
    class="divide-y divide-gray-200 overflow-y-auto border-t"
  >
    <SelectionEntity
      v-for="character in result"
      :key="character.character_id"
      v-model="selected_ids"
      :entity="character"
    />
  </ul>
  <div ref="scrollComponent"></div>
</template>

<script>
import SelectionEntity from "./SelectionEntity";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
    name: "EntitySelection",
    components: {SelectionEntity},
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object
        },
        type: {
            type: String,
            default: () => 'character'
        }
    },
    setup(props) {

        let url = props.type === 'character' ? 'get.affiliated.characters' : 'get.affiliated.corporations'

        if(props.type === 'character') {
            return useInfinityScrolling(url, props.dispatchTransferObject.permission)
        }

        return useInfinityScrolling(url, {
            permission: props.dispatchTransferObject.permission,
            corporation_role: props.dispatchTransferObject.required_corporation_role
        })
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
