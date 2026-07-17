<template>
  <ul
    class="relative z-0 divide-y divide-gray-200 overflow-y-auto border-t"
  >
    <SelectionEntity
      v-for="entity in results"
      :key="entity.id"
      v-model="selected_ids"
      :entity="entity"
    />
  </ul>
</template>

<script>
import SelectionEntity from "./SelectionEntity.vue";
import { router } from "@inertiajs/vue3";
import { getJson } from "@/Functions/http";
import GetAffiliatedCharactersController from "@/actions/Seatplus/Web/Http/Controllers/Shared/GetAffiliatedCharactersController";
import GetAffiliatedCorporationsController from "@/actions/Seatplus/Web/Http/Controllers/Shared/GetAffiliatedCorporationsController";

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
        },
        search: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            results: [],
            initial_ids: [],
            selected_ids: [],
            searchTimer: null,
        }
    },
    computed: {
        changed() {
            return JSON.stringify(this.initial_ids) !== JSON.stringify(this.selected_ids)
        }
    },
    watch: {
        search() {
            clearTimeout(this.searchTimer)
            this.searchTimer = setTimeout(() => this.fetchEntities(), 300)
        }
    },
    beforeMount() {
        // The current selection is carried in the page URL. Ziggy used to serialise it as
        // `character_ids[0]=…`; Inertia serialises `character_ids[]=…` — both share the
        // `${type}_ids` key prefix, so collect every matching entry.
        const params = new URLSearchParams(window.location.search)
        const ids = []

        for (const [key, value] of params.entries()) {
            if (key.startsWith(`${this.type}_ids`)) {
                ids.push(parseInt(value))
            }
        }

        this.selected_ids = ids
        this.initial_ids = [...ids]
    },
    mounted() {
        this.fetchEntities()
    },
    beforeUnmount() {
        clearTimeout(this.searchTimer)

        if (!this.changed) {
            return
        }

        if (this.selected_ids.length === 0) {
            router.get(window.location.pathname)
            return
        }

        router.get(window.location.pathname, { [`${this.type}_ids`]: this.selected_ids })
    },
    methods: {
        async fetchEntities() {
            const query = this.search.length >= 3 ? { search: this.search } : {}
            const permission = this.dispatchTransferObject.permission

            let url

            if (this.type === 'character') {
                url = GetAffiliatedCharactersController.url({ permission }, { query })
            } else {
                const roles = (this.dispatchTransferObject.required_corporation_role ?? []).join(',')

                url = GetAffiliatedCorporationsController.url(
                    roles ? { permission, corporation_role: roles } : { permission },
                    { query },
                )
            }

            const response = await getJson(url)

            this.results = response?.data ?? []
        }
    },
}
</script>

<style scoped>

</style>
