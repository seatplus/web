<template>
  <CardWithHeader
    :key="queryParam"
  >
    <template #header>
      <div class="flex justify-between">
        <EntityBlock :entity="corporation" />
        <div>
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <SearchIcon
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
        <CompleteLoadingHelper
          :key="Object.values(urlParams).join(',')"
          route="corporation.compliance"
          :params="urlParams"
          @results="(results) => rawUsers = results"
        >
          <MemberComplianceListElement
            v-for="(user, index) in users"
            :key="user.id"
            :user="user"
            :can-review="canReview"
            :corporation-id="corporation.corporation_id"
            :even="index%2 === 0"
          />
        </CompleteLoadingHelper>
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "../../../Shared/Layout/Cards/CardWithHeader";
import EntityBlock from "../../../Shared/Layout/Eve/EntityBlock";
import { SearchIcon } from '@heroicons/vue/solid'
import MemberComplianceListElement from "./MemberComplianceListElement";
import {computed, ref, watch} from "vue";
import CompleteLoadingHelper from "../../../Shared/Layout/CompleteLoadingHelper";
export default {
    name: "ComplianceComponent",
    components: {
        CompleteLoadingHelper,
        MemberComplianceListElement,
        EntityBlock, CardWithHeader, SearchIcon},
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
        const search = ref('')
        const urlParams = ref({
            corporation_id: props.corporation.corporation_id,
            type: props.corporation.type
        })

        const users = computed(() => {

            if(props.queryParam === 'renegades') {
                return _.filter(rawUsers.value, (user) => user.count_missing > 0)
            }

            if(props.queryParam === 'loyalists') {
                return _.filter(rawUsers.value, (user) => _.isEqual(user.count_missing, 0))
            }

            return rawUsers.value
        })
        //const canReview = computed(() => usePage().props.value.canReview)

        watch(search,(newValue) => {
            newValue.length >= 3 ? urlParams.value.search = newValue : delete urlParams.value.search
        })

        return {
            rawUsers,
            users,
            search,
            urlParams
        }
    }
}
</script>

<style scoped>

</style>