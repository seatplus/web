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
        <MemberComplianceListElement
          v-for="(user, index) in users"
          :key="user.id"
          :user="user"
          :can-review="canReview"
          :corporation-id="corporation.corporation_id"
          :even="index%2 === 0"
        />
        <li
          v-if="loading"
          class="flex justify-center py-4 text-sm text-gray-500"
        >
          loading resource
        </li>
        <li
          v-else-if="users.length === 0"
          class="flex justify-center py-4 text-sm text-gray-500"
        >
          no entries
        </li>
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
            required: true
        }
    },
    setup(props) {
        const rawUsers = ref([])
        const search = ref('')
        const loading = ref(true)

        const users = computed(() => {

            if(props.queryParam === 'renegades') {
                return _.filter(rawUsers.value, (user) => user.count_missing > 0)
            }

            if(props.queryParam === 'loyalists') {
                return _.filter(rawUsers.value, (user) => _.isEqual(user.count_missing, 0))
            }

            return rawUsers.value
        })

        const load = async () => {
            loading.value = true

            // Only forward the search term once it is specific enough to matter; an empty query
            // fetches the full corporation membership again (mirrors the previous urlParams idiom).
            const query = search.value.length >= 3 ? { search: search.value } : {}

            const url = getCorporationCompliance.url(
                {
                    corporation_id: props.corporation.corporation_id,
                    type: props.corporation.type,
                },
                { query },
            )

            const response = await getJson(url)

            rawUsers.value = response.data
            loading.value = false
        }

        const debouncedLoad = _.debounce(load, 300)

        watch(search, debouncedLoad)

        onMounted(load)

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