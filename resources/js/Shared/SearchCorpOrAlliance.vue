<template>
  <div>
    <!--TODO: add warning if search is less then 3 characters long-->
    <div>
      <label
        for="search"
        class="block text-sm font-medium leading-5 text-gray-700"
      >
        <slot>Search for Corporation or Alliance</slot>
      </label>
      <div class="mt-1 relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <!-- Heroicon name: solid/search -->
          <svg
            :class="['h-5 w-5', error ? 'text-red-500' : 'text-gray-400']"
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
          id="search"
          v-model="query"
          type="text"
          name="search"
          :class="[error ? 'focus:ring-red-500 focus:border-red-500 border-red-300' : 'focus:ring-indigo-500 focus:border-indigo-500 border-gray-300',' block w-full pl-10 sm:text-sm rounded-md']"
          placeholder="corporation or alliance name"
        >
      </div>
      <p
        v-if="error"
        class="mt-2 text-sm text-red-600"
      >
        {{ error[0] }}
      </p>
    </div>

    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-6 sm:mt-5 max-h-96 overflow-auto">
      <li
        v-for="entity of entities"
        :key="entity.id"
        class="col-span-1 bg-white rounded-lg shadow"
        :class="{'bg-green-50' : isSelected(entity)}"
        @click="flipSelect(entity)"
      >
        <div class="w-full flex items-center justify-between p-6 space-x-6">
          <EntityByIdBlock
            :id="entity.id"
            :image-size="10"
            name-font-size="sm"
          />
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios';
import EntityByIdBlock from "./Layout/Eve/EntityByIdBlock";
import {ref, watch} from "vue";
import route from "ziggy";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "SearchCorpOrAlliance",
    components: {EntityByIdBlock},
    props: {
        modelValue: {
            type: Array,
            default: () => []
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {

        const selected = ref(props.modelValue)
        const entities = ref([])
        const isLoading = ref(false)
        const query = ref('')
        const error = ref(usePage().props.value.errors.selectedEntities)

        const isSelected = function(entity) {

            return !!_.find(selected.value, {id: entity.id})
            //return selected.value.includes(entity_id)
        }

        watch(query, () => {
            search()
        })

        const search = _.debounce(async () => {
            if (query.value.length < 3 || isLoading.value) {
                entities.value = []
                return
            }

            isLoading.value = true

            await axios
                .get(route('search.alliance.corporation', query.value))
                .then(results => {

                    let alliance_ids = _.map(_.get(results.data, 'alliance', []), (entity_id) => {
                        return {
                            id: entity_id,
                            type: 'alliance'
                        }
                    })

                    let corporation_ids = _.map(_.get(results.data, 'corporation', []), (entity_id) => {
                        return {
                            id: entity_id,
                            type: 'corporation'
                        }
                    })

                    entities.value = [...alliance_ids, ...corporation_ids]

                })
                .catch(error => {
                    console.log(error);
                })

            isLoading.value = false
        }, 250)

        const flipSelect = (entity) => {

            if(isSelected(entity.id)) {
                selected.value = _.remove(this.selected, (select) => select.id !== entity.id)
            } else {
                selected.value.push(entity)
            }

            emit('update:modelValue', selected.value)
        }

        return {
            isSelected,
            query,
            flipSelect,
            error,
            entities
        }

    }
}
</script>

<style scoped>

</style>
