<template>
  <div>
    <!--TODO: create a list and let user select the propper entry right now its good enough-->
    <!--<input type="text" v-model="corpOrAlliance.name" @input="performSearch">-->
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
          v-model="search"
          id="search"
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

    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-6 sm:mt-5">
      <li
        v-for="entity of corpOrAlliances"
        :key="entity.id"
        class="col-span-1 bg-white rounded-lg shadow"
        :class="{'bg-green-50' : isSelected(entity)}"
        @click="flipSelect(entity)"
      >
        <div class="w-full flex items-center justify-between p-6 space-x-6">
          <EntityBlock
            :entity="entity"
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
import EntityBlock from "./Layout/Eve/EntityBlock";

export default {
    name: "SearchCorpOrAlliance",
    components: {EntityBlock},
    props: ['modelValue'],
    emits: ['update:modelValue'],
    data() {
        return {
            corpOrAlliances: [],
            search: '',
            selected: this.modelValue,
            error: this.$page.props.errors.selectedEntities
        }
    },
    computed: {
        selectedIds() {
            return _.map(this.selected, (entity) => entity.id)
        }
    },
    watch: {
        selected(newValue) {
            this.$emit('update:modelValue', newValue)
        },
        search() {
            if (this.search.length < 3)
                return

            axios
                .get(this.$route('search.alliance.corporation', this.search))
                .then(results => {

                    this.corpOrAlliances = _.map(results.data, (result) => {

                        let object = {
                            id: result.id,
                            name: result.name,
                            type: result.category
                        }

                        if(result.type === 'corporation') {
                            object.corporation_id = result.id
                        } else {
                            object.alliance_id= result.id
                        }

                        return object

                    })

                })
                .catch(error => {
                    console.log(error);
                })
        }
    },
    methods: {
        flipSelect(entity) {

            let index = this.selectedIds.indexOf(entity.id)

            if(index >= 0)
                return this.removeSelected(entity)

            this.selected.push(entity)
        },
        isSelected(entity) {
            return this.selected.includes(entity)
        },
        removeSelected(entity) {
            this.selected = _.remove(this.selected, (select) => select.id !== entity.id)
        }
    }
}
</script>

<style scoped>

</style>
