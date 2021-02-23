<template>
    <div>
        <VueAutosuggest
            v-model="query"
            :suggestions="filteredSuggestions"
            :input-props="{
                        placeholder: placeholder,
                        class: 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md'
                    }"
            @input="onInputChange"
            @selected="onSelected"
            @click="clickHandler"
            :component-attr-class-autosuggest-results-container="showSuggestions ? 'max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm': 'hidden'"
        >
            <template slot="before-input">
                <label class="block text-sm font-medium text-gray-700">{{ label }}</label>
            </template>


            <template  slot-scope="{suggestion}">
                <div class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-3 pr-9">
                <span :class="['font-normal block truncate', suggestion.item.id ? 'font-semibold' : 'font-normal' ]">
                    {{suggestion.item.name}}
                </span>
                </div>
            </template>

        </VueAutosuggest>

        <span v-for="selection in selections" :key="selection.id" class="inline-flex items-center py-0.5 pl-2 pr-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
            {{ selection.name }}
            <button @click="removeEntry(selection.id)" type="button" class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white">
                <span class="sr-only">Remove {{selection.name}}</span>
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                </svg>
            </button>
        </span>

    </div>

</template>

<script>
import { VueAutosuggest } from "vue-autosuggest"
export default {
    name: "Multiselect",
    components: {VueAutosuggest},
    props: {
        value: {
            required: true
        },
        route: {
            required: true,
            type: String
        },
        label: {
            required: true,
            type: String
        },
        placeholder: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            search_result: [],
            selections: this.value,
            query: '',
            suggestions: [],
            showSuggestions: false,
        }
    },
    watch: {
        value(newVal) {
            this.selections = newVal
        },
    },
    methods: {
        clickHandler(item) {
            // event fired when clicking on the input
        },
        onSelected(item) {

            this.selections.push(item.item)

            this.showSuggestions = false;
            this.query = ''

            this.$emit('input', this.selections)
        },
        onInputChange(query) {

            if (query === undefined) {
                return;
            }

            this.showSuggestions = true

            if(this.query.length > 2)
                return axios.get(this.$route(this.route, { search: query }))
                    .then((result) => this.search_result = result.data)

            this.search_result = []

        },
        removeEntry(id) {

            let selections = this.selections.filter(suggestion => suggestion.id !== id)

            this.$emit('input', selections)
        }
    },
    computed: {
        filteredSuggestions() {
            return [
                {
                    data: _.filter(this.search_result, result => _.findIndex(this.selections, ['id', result.id]) === -1)
                }
            ]
        },
    }
}
</script>

<style scoped>

</style>
