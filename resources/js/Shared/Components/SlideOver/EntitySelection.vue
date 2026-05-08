<template>
  <ul
    ref="scrollComponent"
    class="relative z-0 divide-y divide-gray-200 overflow-y-auto border-t"
  >
    <InfiniteLoadingHelper
      :key="url"
      v-slot="{results}"
      :url="url"
    >
      <SelectionEntity
        v-for="character in results"
        :key="character.character_id"
        v-model="selected_ids"
        :entity="character"
      />
    </InfiniteLoadingHelper>
  </ul>
  <div ref="scrollComponent" />
</template>

<script>
import SelectionEntity from "./SelectionEntity.vue";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper.vue";
import {computed, ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import { characters as affiliatedCharacters, corporations as affiliatedCorporations } from '@/routes/get/affiliated'

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

        const params = ref(props.type === 'character'
            ? { permission: props.dispatchTransferObject.permission }
            : { permission: props.dispatchTransferObject.permission, corporation_role: props.dispatchTransferObject.required_corporation_role}
        )

        const search = computed(() => props.search)

        watch(search,(newValue) => {
            newValue.length >= 3 ? params.value.search = newValue : delete params.value.search
        })

        const url = computed(() => {
            const { permission, corporation_role, search: searchTerm } = params.value
            const queryOptions = searchTerm ? { query: { search: searchTerm } } : {}

            if (props.type === 'character') {
                return affiliatedCharacters(permission, queryOptions).url
            }
            return affiliatedCorporations({ permission, corporation_role }, queryOptions).url
        })

        return {
            params,
            url
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

        const urlParams = new URLSearchParams(window.location.search)
        const ids = urlParams.getAll(`${this.type}_ids[]`)

        if(!ids.length)
            return

        this.selected_ids = ids.map(id => parseInt(id))
        this.initial_ids = ids.map(id => parseInt(id))

    },
    beforeUnmount() {

        if(!this.changed)
            return

        const currentPath = usePage().url.split('?')[0]

        if(_.isEmpty(this.selected_ids))
            return router.get(currentPath)

        const queryParameter = this.type === 'character'
            ? { character_ids: this.selected_ids }
            : { corporation_ids: this.selected_ids }

        router.get(currentPath, queryParameter)
    },
}
</script>

<style scoped>

</style>
