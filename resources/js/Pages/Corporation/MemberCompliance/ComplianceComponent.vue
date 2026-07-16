<template>
  <CardWithHeader
    :key="queryParam"
  >
    <template #header>
      <div class="flex justify-between">
        <EntityBlock :entity="corporation" />
        <div>
          <div class="mt-1 relative rounded-md shadow-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <input
              id="search"
              v-model="search"
              type="search"
              name="search"
              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
              placeholder="character name"
            >
          </div>
        </div>
      </div>
    </template>

    <div class="relative max-h-96 overflow-y-auto">
      <div class="hidden sm:grid grid-cols-12 gap-x-0 gap-y-1 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-3 py-1 col-span-3">
          Main Character
        </div>
        <div
          class="px-3 py-1"
          :class="canReview ? 'col-span-8' : 'col-span-9'"
        >
          Characters
        </div>
        <div
          v-if="canReview"
          class="px-3 py-1 col-span-1"
        >
          Review
        </div>
      </div>

      <ul class="relative z-0">
        <li
          v-if="loading"
          class="flex justify-center py-6"
        >
          <svg
            class="animate-spin h-6 w-6 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
        </li>
        <li
          v-else-if="!users.length"
          class="text-center text-sm text-gray-500 py-6"
        >
          no entries
        </li>
        <template v-else>
          <MemberComplianceListElement
            v-for="(user, index) in users"
            :key="user.id"
            :user="user"
            :can-review="canReview"
            :corporation-id="corporation.corporation_id"
            :even="index%2 === 0"
          />
        </template>
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import MemberComplianceListElement from "./MemberComplianceListElement.vue";
import {computed, onMounted, ref, watch} from "vue";
import { getJson } from "@/Functions/http";
import { getCorporationCompliance } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController";
export default {
    name: "ComplianceComponent",
    components: {
        MemberComplianceListElement,
        EntityBlock, CardWithHeader, MagnifyingGlassIcon},
    props: {
        corporation: {
            type: Object,
            required: true
        },
        queryParam: {
            type: String,
            required: true
        },
        canReview: {
            type: Boolean,
            required: true,
            default: false
        }
    },
    setup(props) {
        const rawUsers = ref([])
        const loading = ref(true)
        const search = ref('')

        // Full (unpaginated) compliance list for this corporation, fetched axios/Ziggy-free.
        // `search` (>=3 chars) is applied server-side; queryParam filtering is client-side below.
        const fetchUsers = async () => {
            loading.value = true
            try {
                const query = search.value.length >= 3 ? { search: search.value } : {}
                const response = await getJson(getCorporationCompliance.url(
                    { corporation_id: props.corporation.corporation_id, type: props.corporation.type },
                    { query },
                ))
                rawUsers.value = response?.data ?? []
            } finally {
                loading.value = false
            }
        }

        onMounted(fetchUsers)

        // Refetch (debounced) only when the effective search term changes (>=3 chars, or cleared).
        let lastQuery = ''
        const maybeRefetch = _.debounce(() => {
            const query = search.value.length >= 3 ? search.value : ''
            if (query === lastQuery) {
                return
            }
            lastQuery = query
            fetchUsers()
        }, 300)
        watch(search, maybeRefetch)

        const users = computed(() => {

            if(props.queryParam === 'renegades') {
                return _.filter(rawUsers.value, (user) => user.count_missing > 0)
            }

            if(props.queryParam === 'loyalists') {
                return _.filter(rawUsers.value, (user) => _.isEqual(user.count_missing, 0))
            }

            return rawUsers.value
        })

        return {
            rawUsers,
            users,
            search,
            loading,
        }
    }
}
</script>

<style scoped>

</style>