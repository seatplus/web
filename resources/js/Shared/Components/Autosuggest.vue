<template>
  <Listbox v-model="selected">
    <ListboxLabel
      v-if="label"
      class="block text-sm font-medium text-gray-700"
    >
      {{ label }}
    </ListboxLabel>
    <input
      :id="label"
      v-model="query"
      type="text"
      :name="label"
      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
      :placeholder="placeholder"
      @click="toggle"
      @keyup.delete="handleBackspace"
    >
    <div v-show="open">
      <div
        class="absolute inset-0 bg-transparent"
        @click="toggle"
      />
      <ListboxOptions
        static
        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
      >
        <ListboxOption
          v-for="option in options"
          :key="option"
          v-slot="{ selected }"
          :value="option"
          class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-8 pr-4"
        >
          <EntityBlock
            v-if="option.hasEveImage"
            :entity="option"
            class="block truncate"
            :image-size="5"
            :name-class="selected ? 'font-semibold' : 'font-medium' + ' ' + 'text-sm leading 6 text-gray-900'"
          />
          <div v-else>
            {{ option.name }}
          </div>
          <span
            v-show="selected"
            class="absolute inset-y-0 left-0 flex items-center pl-1.5"
          >
            <CheckIcon class="h-5 w-5" />
          </span>
          <ListboxOption />
        </listboxoption>
      </ListboxOptions>
    </div>
  </listbox>
</template>

<script>
import {CheckIcon} from "@heroicons/vue/solid";
import {
    Listbox,
    ListboxOptions,
    ListboxOption,
    ListboxLabel
} from '@headlessui/vue'
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";

export default {
    name: "Autosuggest",
    components: {EntityBlock, Listbox, ListboxOptions, ListboxOption, CheckIcon, ListboxLabel},
    props: {
        route: {
            required: true,
            type: String
        },
        label: {
            required: false,
            type: String
        },
        placeholder: {
            required: true,
            type: String
        },
        routeParameters: {
            required: false,
            type: Object,
            default: () => {
            }
        }
    },
    emits: ['selected', 'selectedObject'],
    data() {
        return {
            search_result: [],
            selected: null,
            query: '',
            suggestions: [],
            open: false,
        }
    },
    computed: {
        options() {
            return _.isArray(this.suggestions) ? this.suggestions : _.get(this.suggestions, 'data', [])
        }
    },
    watch: {
        query(query) {

            if (query === undefined) {
                return;
            }

            /*if (query === '')
              this.$emit('selected', null)*/

            // In case of a select, the query gets updated, we need to prevent the suggestions from showing again.
            if (query === _.get(this.selected, 'name')) {
                return;
            }

            if (query.length <= 2)
                return;

            let self = this

            //let $queryParams = _.merge({search: query}, this.routeParameters)

            return axios.get(this.route(this.route, {search: query, ...this.routeParameters}))
                .then((result) => {
                    self.suggestions = result.data

                    // if previously the suggestions were not shown toggle them
                    if (!this.open)
                        this.toggle()
                } /*console.log(result.data)*/)
        },
        selected(newValue) {
            this.query = _.get(newValue, 'name')
            this.open = false
            this.$emit('selected', _.get(newValue, 'id'))
            this.$emit('selectedObject', newValue)
        }
    },
    methods: {
        select(suggestion) {
            //TODO: Delete
            this.selected = suggestion
            this.query = _.get(suggestion, 'name')
            this.open = false
            this.$emit('selected', suggestion.id)
            this.$emit('selectedObject', suggestion)
        },
        toggle() {

            if (this.options.length > 0)
                this.open = !this.open

            /*if (this.open)
                new createPopper(this.$refs.inputField, this.$refs.listboxCollabsible, {
                    placement: 'bottom-start',
                    modifiers: [
                        {
                            name: "sameWidth",
                            enabled: true,
                            phase: "beforeWrite",
                            requires: ["computeStyles"],
                            fn: ({state}) => {
                                state.styles.popper.width = `${state.rects.reference.width}px`;
                            },
                            effect: ({state}) => {
                                state.elements.popper.style.width = `${
                                    state.elements.reference.offsetWidth
                                }px`;
                            },
                        },
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 8],
                            },
                        },
                    ]
                })*/

        },
        handleBackspace() {

            if (this.query.length > 2)
                return;

            this.open = false
            this.suggestions = []
            this.selected = null
            this.$emit('selected', null)
        }
    }

}
</script>

<style scoped>

</style>
