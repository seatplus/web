<template>
  <div>
    <div class="bg-white overflow-hidden shadow rounded-lg mb-3">
      <div class="px-4 py-5 sm:px-6">
        <!-- Content goes here -->
        <div class="md:flex md:items-center md:justify-between">
          <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
            Settings List
          </h3>
          <div class="flex shrink-0 items-center space-x-2">
            <span class="text-sm font-medium text-gray-900">Affiliate everything</span>
            <SimpleToggle v-model="affiliateEverything" />
          </div>
        </div>

        <p class="mt-1 text-sm leading-5 text-gray-500">
          Setup the access group and define what anyone in this group should be able to achieve for which asset. F.e. if this is your recruiter access contol group you would define f.e. that a recruiter should have access to assets of characters in any but (inverse) a specific corporation. Note: you will be able to assign the access control group to users in another step.
        </p>
      </div>
      <div v-show="!affiliateEverything">
        <!--<div class="w-full flex md:ml-0 px-6">
          <label
            for="search_field"
            class="sr-only"
          >
            Search
          </label>
          <div class="relative w-full text-gray-400 focus-within:text-gray-600">
            <div class="ml-2 absolute inset-y-0 left-0 flex items-center pointer-events-none">
              <svg
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                />
              </svg>
            </div>
            <input
              id="search_field"
              v-model="query"
              class="block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 sm:text-sm"
              placeholder="Search"
              type="search"
            >
          </div>
        </div>-->
        <InputWithValidation
          v-model="query"
          label="Search"
          placeholder="Search"
          class="px-6"
          :warning="showWarning ? 'No results found' : ''"
        >
          <template #description>
            <TransitionRoot
              :show="showWarning"
              enter="transition-opacity duration-75"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="transition-opacity duration-150"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="mt-2 text-sm">
                <div
                  class="border-l-4 border-yellow-400 bg-yellow-50 p-4"
                >
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <ExclamationTriangleIcon
                        class="h-5 w-5 text-yellow-400"
                        aria-hidden="true"
                      />
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-yellow-700">
                        You have no character refresh token with required scope.
                        {{ ' ' }}
                        <Link
                          :href="route('enable_esi_search')"
                          class="font-medium text-yellow-700 underline hover:text-yellow-600"
                        >
                          Upgrade one token to be able to use this search.
                        </Link>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </TransitionRoot>
          </template>
        </InputWithValidation>

        <div
          class="overflow-auto overflow-x-hidden h-64"
        >
          <ListTransition :entries="filteredEntities">
            <div
              v-for="(entity, index) in filteredEntities"
              :key="entity.id"
              class="flex flex-wrap"
            >
              <div :class="[index%2 === 0 ? 'bg-white' : 'bg-gray-50', 'w-full sm:w-1/2 py-4 px-6']">
                <EntityByIdBlock :id="entity.id" />
              </div>
              <div :class="[index%2 === 0 ? 'bg-white' : 'bg-gray-50', 'w-full sm:w-1/2 py-4 px-6 text-right']">
                <span class="relative z-0 inline-flex  rounded-md">
                  <span class="relative inline-flex items-center text-sm leading-5 font-medium text-gray-700 px-4">
                    Add to ...
                  </span>
                  <button
                    type="button"
                    class="relative inline-flex shadow-sm items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                    @click="addAffiliation('allowed', entity)"
                  >
                    Allowed
                  </button>
                  <button
                    type="button"
                    class="-ml-px relative inline-flex shadow-sm items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                    @click="addAffiliation('inverse', entity)"
                  >
                    Inverse
                  </button>
                  <button
                    type="button"
                    class="-ml-px relative inline-flex shadow-sm items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                    @click="addAffiliation('forbidden', entity)"
                  >
                    Forbidden
                  </button>
                </span>
              </div>
            </div>
          </ListTransition>
        </div>
      </div>
    </div>

    <div
      v-show="!affiliateEverything"
      class="mb-3 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none"
    >
      <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
        <div class="flex-1 bg-white flex flex-col justify-between">
          <div class="flex-1 p-6 ">
            <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
              Allowed
            </h3>
            <p class="mt-3 text-base leading-6 text-gray-500">
              List of allowed entities and their predecessors. For example a selected corporation will allow access to corporation and all its members.
            </p>
          </div>
          <AffiliationList
            v-model="affiliationsValue"
            type="allowed"
          />
        </div>
      </div>
      <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
        <div class="flex-1 bg-white flex flex-col justify-between">
          <div class="flex-1 p-6 ">
            <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
              Inverse
            </h3>
            <p class="mt-3 text-base leading-6 text-gray-500">
              List of inverse entities and their predecessors. For example a selected alliance will allow access to all but the listed alliance, its corporations and all its members.
            </p>
          </div>
          <AffiliationList
            v-model="affiliationsValue"
            type="inverse"
          />
        </div>
      </div>
      <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
        <div class="flex-1 bg-white flex flex-col justify-between">
          <div class="flex-1 p-6 ">
            <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
              Forbidden
            </h3>
            <p class="mt-3 text-base leading-6 text-gray-500">
              No matter what, selected entities and its predecessors will never be able to be accessed.
            </p>
          </div>
          <AffiliationList
            v-model="affiliationsValue"
            type="forbidden"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import ListTransition from "@/Shared/Transitions/ListTransition.vue"
import AffiliationList from "./AffiliationList.vue"
import {computed, onBeforeMount, ref, watch, watchEffect} from "vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import SimpleToggle from "@/Shared/SimpleToggle.vue";
import InputWithValidation from "@/Shared/Layout/Forms/InputWithValidation.vue";
import {TransitionRoot} from "@headlessui/vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/20/solid';
import { Link } from '@inertiajs/vue3';

export default {
    name: "EditSettings",
    components: {TransitionRoot, InputWithValidation, SimpleToggle, EntityByIdBlock, AffiliationList, ListTransition, ExclamationTriangleIcon, Link},
    props: {
        affiliations: {
            type: Array,
            required: false
        }
    },
    emits: ['update:affiliations'],
    setup(props, {emit}) {
        const entities = ref([])
        const query = ref('')
        const affiliationsValue = ref(props.affiliations)
        const affiliateEverything = ref(!!_.find(props.affiliations, {'id' : 1000001}))

        const hasToken = ref(null);

        const fetchData = _.debounce(async () => {

            await axios.get(route('acl.search.affiliatable', { query: query.value.length > 2 ? query.value : '' }))
                .then(result => {

                    entities.value = result.data.data
                })
                .catch(error => console.log(error))
        }, 300)

        const filteredEntities = computed( () => {

            const affiliatedIds = _.map(affiliationsValue.value, (affiliation) => affiliation.id)

            return _.chain(entities.value)
                .filter(entity => !_.includes(affiliatedIds, entity.id))
                .value()
        })

       const addAffiliation = function (type, entity) {
           affiliationsValue.value.push({
               id: entity.id,
               category: entity.category,
               type: type,
           })
        }

        const checkToken = async () => {

            // If hasToken is null, we don't know yet if the user has a token
            if (_.isNull(hasToken.value)) {
                // check if the user has a token with required scope
                await axios.get(route('autosuggestion.token'))
                    .then(response => {
                        // if the user has a token, set hasToken to true
                        // we don't need to check again
                        // we expect the response to be a 1 or 0 and turn it into a boolean
                        hasToken.value = !!response.data;
                    }).catch(error => {
                        console.log(error)
                    })
            }
        }

        watch([affiliationsValue, affiliateEverything], (newAffiliationsValue, affiliateEverythingValue) => {

            emit('update:affiliations', !affiliateEverythingValue ? newAffiliationsValue : [{
                id: 1000001,
                category: "corporation",
                type: "inverse",
            }])
        })

        watchEffect(async () => {

            if (query.value === undefined) {
                return;
            }

            if (hasToken.value === false) {
                return;
            }

            await checkToken();

            if (hasToken.value === false) {
                return;
            }

            if (query.value.length < 3) {
                return
            }

            await fetchData();
        })

        const showWarning = computed(() => {

            if (query.value.length < 1) {
                return false;
            }

            return !_.isNull(hasToken.value) && !hasToken.value
        })

        onBeforeMount(() => {
            fetchData()
        })

        return {
            filteredEntities,
            entities,
            query,
            addAffiliation,
            affiliationsValue,
            affiliateEverything,
            showWarning
        }

    },
    data() {
        return {
            search: null
        }
    },
    watch: {
        entities() {
            let affiliations = ['allowed', 'inverse', 'forbidden']

            _.each(affiliations, (affiliation) => this[affiliation] = this.getAffiliatedEntities(affiliation))
        }
    },
    methods: {
        getInfo: async function (url, info = []) {
            return await axios
                .get(url)
                .then((response) => {

                    response.data.data.forEach(object => info.push(object))

                    if (_.isNull(response.data.links.next))
                        return info

                    return this.getInfo(response.data.links.next, info)
                })
                .catch(error => console.log(error))
        },
        getAffiliatedEntities(type) {
            let filtered_affiliations =  _.filter(this.existingAfffiliations, (affiliation) => {return _.isEqual(affiliation.type, type)})

            return _.map(filtered_affiliations, (affiliation) => {

                return _.find(this.entities, {id: affiliation.affiliatable_id})
            })
        },
        isEmpty(array) {
            return _.isEmpty(array)
        },
    }
}
</script>

<style scoped>

</style>
