<template>
    <ModalWithFooter v-model="open">
        <template v-slot:symbol>
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

        </template>
        <template v-slot:title>
            <span>Add location information for unknown structure ({{ location_id }})</span>
        </template>
        <template v-slot:description>
            <div class="space-y-4">

                <div>
                    <label for="location_name" class="block text-sm font-medium text-gray-700">Structure name</label>
                    <div class="mt-1 relative shadow-sm rounded-md">
                        <input v-model="form.name" type="text" name="location_name" id="location_name"
                               :class="[form.errors.name ? ' focus:ring-red-500 border-red-300 text-red-900 focus:outline-none placeholder-red-300 focus:border-red-500' : 'focus:ring-indigo-500 focus:border-indigo-500 border-gray-300' ,'block w-full pr-10  sm:text-sm rounded-md']"
                               placeholder="Structure name">
                        <div v-if="form.errors.name" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <!-- Heroicon name: solid/exclamation-circle -->
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-red-600" id="ename-error" v-if="form.errors.name">{{ form.errors.name.find(Boolean) }}</div>
                </div>

<!--                <VueAutosuggest
                    v-model="query"
                    :suggestions="filteredSuggestions"
                    :input-props="{
                        placeholder: 'search for solar system',
                        class: 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md'
                    }"
                    @input="onInputChange"
                    @selected="onSelected"
                    @click="clickHandler"
                    component-attr-class-autosuggest-results-container="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                >
                    <template slot="before-input">
                        <label class="block text-sm font-medium text-gray-700">Solar system</label>
                    </template>


                    <template  slot-scope="{suggestion}">
                        <div class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-3 pr-9">
                            <span :class="['font-normal block truncate', suggestion.item.id === form.solar_system_id ? 'font-semibold' : 'font-normal' ]">
                                {{suggestion.item.name}}
                            </span>
                        </div>
                    </template>

                </VueAutosuggest>-->
                <div class="mt-2 text-sm text-red-600" id="solar_system-error" v-if="form.errors.solar_system_id">{{ form.errors.solar_system_id.find(Boolean) }}</div>
            </div>

        </template>
        <template v-slot:buttons>
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
              <button @click="submit()" :disabled="form.processing" type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Submit
                </button>
            </span>
        </template>
    </ModalWithFooter>
</template>

<script>
import ModalWithFooter from "@/Shared/Modals/ModalWithFooter";
//TODO import { VueAutosuggest } from "vue-autosuggest"
export default {
    name: "AddManualLocationModal",
    components: {ModalWithFooter, /*VueAutosuggest*/},
    props: {
        value: {
            required: true,
            type: Boolean
        },
        location_id: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            open: this.value,
            search_result: [],
            query: '',
            suggestions: [],
            showSuggestions: false,
            form: this.$inertia.form({
                name: '',
                solar_system_id: null,
            }),
        }
    },
    watch: {
        value(newVal) {
            this.open = newVal
        },
        open(newVal) {
            this.$emit('input', newVal)
        }
    },
    methods: {
        clickHandler(item) {
            // event fired when clicking on the input
        },
        onSelected(item) {
            this.form.solar_system_id = item?.item?.id;
            this.showSuggestions = false
        },
        onInputChange(text) {
            // event fired when the input changes
            this.showSuggestions = true

            if(this.query.length > 2)
                return axios.get(this.$route('resolve.solar_system', this.query))
                    .then((result) => this.suggestions = result.data)

            this.suggestions = []

        },
        submit() {
            let self = this;

            this.form.transform((data) => ({
                ...data,
                location_id: this.location_id
            })).post(this.$route('post.manual_location'), {
                onSuccess: () => {
                    self.form.reset()
                    self.suggestions = []
                    self.query = ''
                    self.open = false
                }
            })



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
        }
    }
}
</script>

<style scoped>

</style>
