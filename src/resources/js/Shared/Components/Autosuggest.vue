<template>
<!--    <VueAutosuggest
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
                <span :class="['font-normal block truncate', suggestion.item.id === selectedId ? 'font-semibold' : 'font-normal' ]">
                    {{suggestion.item.name}}
                </span>
            </div>
        </template>

    </VueAutosuggest>-->
</template>

<script>
/* TODO import { VueAutosuggest } from "vue-autosuggest"*/
export default {
    name: "Autosuggest",
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
            selected: null,
            query: '',
            suggestions: [],
            showSuggestions: false,
        }
    },
    watch: {
        value(newVal) {
            this.query = _.get(_.find(this.suggestions, {id: newVal}), 'name')
        },
    },
    methods: {
        clickHandler(item) {
            // event fired when clicking on the input
        },
        onSelected(item) {

            this.$emit('input', item?.item?.id)
            this.selected = item?.item;

            this.showSuggestions = false
        },
        onInputChange(query) {

            if (query === undefined) {
                return;
            }

            if (query === '')
                this.$emit('input', null)

            let self = this

            this.showSuggestions = true

            if(this.query.length > 2)
                return axios.get(this.$route(this.route, { search: query }))
                    .then((result) => self.suggestions = result.data /*console.log(result.data)*/)

            this.suggestions = []

        }
    },
    computed: {
        filteredSuggestions() {
            return [
                {
                    data: this.suggestions.filter(item => {
                        return (
                            item.name.toLowerCase().indexOf(this.query.toLowerCase()) > -1
                        );
                    })
                }
            ];
        },
        selectedId() {
            return _.get(this.selected, 'id')
        }
    }
}
</script>

<style scoped>

</style>
