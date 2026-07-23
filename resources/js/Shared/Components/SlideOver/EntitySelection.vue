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

<script setup>
import { computed, onBeforeMount, onBeforeUnmount, ref, watch } from "vue";
import { router, usePage, WhenVisible } from "@inertiajs/vue3";
import SelectionEntity from "./SelectionEntity.vue";

const props = defineProps({
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
});

const page = usePage();

const initial_ids = ref([])
const selected_ids = ref([])
const searchTimer = ref(null)

// The picker's affiliated list arrives as the lazily-resolved `affiliatedEntities` shared
// prop; undefined until <WhenVisible> has fired its first partial reload once the list
// scrolls into view.
const results = computed(() => page.props.affiliatedEntities ?? [])

const permission = computed(() => props.dispatchTransferObject.permission)

const corporationRoles = computed(() => (props.dispatchTransferObject.required_corporation_role ?? []).join(','))

// Mirror the old behaviour: only filter server-side once at least 3 characters are typed.
const searchParam = computed(() => props.search.length >= 3 ? props.search : '')

// The picker context that rides along with the partial reload so the shared closure can
// resolve + filter the right entities. `preserveUrl` keeps this internal context out of the
// page URL (which the picker itself uses only for the `${type}_ids` selection).
const reloadParams = computed(() => ({
    data: {
        type: props.type,
        permission: permission.value,
        corporationRoles: corporationRoles.value,
        // Namespaced so the picker's search never collides with a page's own `search`
        // query filter (e.g. the assets page) during the partial reload.
        search_aff: searchParam.value,
    },
    preserveUrl: true,
}))

const changed = computed(() => JSON.stringify(initial_ids.value) !== JSON.stringify(selected_ids.value))

function reloadEntities() {
    router.reload({
        only: ['affiliatedEntities'],
        data: reloadParams.value.data,
        preserveUrl: true,
    })
}

// Debounced re-request on search change. <WhenVisible> only fires on its initial
// intersection, so once the list is loaded a search change is served by an explicit partial
// reload of the same shared prop (the on-demand equivalent of the old debounced getJson).
watch(() => props.search, () => {
    clearTimeout(searchTimer.value)
    searchTimer.value = setTimeout(() => reloadEntities(), 300)
})

onBeforeMount(() => {
    // The current selection is carried in the page URL. Ziggy used to serialise it as
    // `character_ids[0]=…`; Inertia serialises `character_ids[]=…` — both share the
    // `${type}_ids` key prefix, so collect every matching entry.
    const params = new URLSearchParams(window.location.search)
    const ids = []

    for (const [key, value] of params.entries()) {
        if (key.startsWith(`${props.type}_ids`)) {
            ids.push(parseInt(value))
        }
    }

    selected_ids.value = ids
    initial_ids.value = [...ids]
})

onBeforeUnmount(() => {
    clearTimeout(searchTimer.value)

    if (!changed.value) {
        return
    }

    if (selected_ids.value.length === 0) {
        router.get(window.location.pathname)
        return
    }

    router.get(window.location.pathname, { [`${props.type}_ids`]: selected_ids.value })
})
</script>

<style scoped>

</style>
