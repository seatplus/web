<template>
  <WhenVisible
    data="affiliatedEntities"
    :params="reloadParams"
    :buffer="200"
  >
    <template #fallback>
      <ul class="relative z-0 divide-y divide-gray-200 border-t">
        <li
          v-for="n in 4"
          :key="n"
          class="px-6 py-5"
        >
          <div class="flex items-center space-x-3 animate-pulse">
            <div class="h-10 w-10 rounded-full bg-gray-200" />
            <div class="h-4 w-1/2 rounded bg-gray-200" />
          </div>
        </li>
      </ul>
    </template>

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
  </WhenVisible>
</template>

<script>
import SelectionEntity from "./SelectionEntity.vue";
import { router, WhenVisible } from "@inertiajs/vue3";

export default {
    name: "EntitySelection",
    components: {SelectionEntity, WhenVisible},
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
            initial_ids: [],
            selected_ids: [],
            searchTimer: null,
        }
    },
    computed: {
        // The picker's affiliated list arrives as the lazily-resolved `affiliatedEntities` shared
        // prop; undefined until <WhenVisible> has fired its first partial reload once the list
        // scrolls into view.
        results() {
            return this.$page.props.affiliatedEntities ?? []
        },
        permission() {
            return this.dispatchTransferObject.permission
        },
        corporationRoles() {
            return (this.dispatchTransferObject.required_corporation_role ?? []).join(',')
        },
        // Mirror the old behaviour: only filter server-side once at least 3 characters are typed.
        searchParam() {
            return this.search.length >= 3 ? this.search : ''
        },
        // The picker context that rides along with the partial reload so the shared closure can
        // resolve + filter the right entities. `preserveUrl` keeps this internal context out of the
        // page URL (which the picker itself uses only for the `${type}_ids` selection).
        reloadParams() {
            return {
                data: {
                    type: this.type,
                    permission: this.permission,
                    corporationRoles: this.corporationRoles,
                    // Namespaced so the picker's search never collides with a page's own `search`
                    // query filter (e.g. the assets page) during the partial reload.
                    search_aff: this.searchParam,
                },
                preserveUrl: true,
            }
        },
        changed() {
            return JSON.stringify(this.initial_ids) !== JSON.stringify(this.selected_ids)
        }
    },
    watch: {
        // Debounced re-request on search change. <WhenVisible> only fires on its initial
        // intersection, so once the list is loaded a search change is served by an explicit partial
        // reload of the same shared prop (the on-demand equivalent of the old debounced getJson).
        search() {
            clearTimeout(this.searchTimer)
            this.searchTimer = setTimeout(() => this.reloadEntities(), 300)
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
        reloadEntities() {
            router.reload({
                only: ['affiliatedEntities'],
                data: this.reloadParams.data,
                preserveUrl: true,
            })
        }
    },
}
</script>

<style scoped>

</style>
