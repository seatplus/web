<template>
    <div>
        <!--TODO: create a list and let user select the propper entry right now its good enough-->
        <!--<input type="text" v-model="corpOrAlliance.name" @input="performSearch">-->
        <!--TODO: add warning if search is less then 3 characters long-->

        <div>
            <label for="search" class="block text-sm font-medium leading-5 text-gray-700">
                <slot>Search for Corporation or Alliance</slot>
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg :class="['h-5 w-5', error ? 'text-red-500' : 'text-gray-400']" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"></path>
                    </svg>
                </div>
                <input v-model="search" @input="performSearch"
                       id="search"
                       :class="[{'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red': error}, 'form-input block w-full pl-10 sm:text-sm sm:leading-5']"
                       placeholder="Amok."
                />
                <div v-if="error" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p v-if=error class="mt-2 text-sm text-red-600">{{error[0]}}</p>
        </div>

        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-6 sm:mt-5">
            <li :key="entity.id" v-for="entity of corpOrAlliances" @click="flipSelect(entity)" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ entity.name }}</h3>
                            <span v-if="isSelected(entity)" class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Selected</span>
                        </div>
                        <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ entity.category }}</p>
                    </div>
                    <EveImage :object="entity" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
                </div>
            </li>
        </ul>

    </div>
</template>

<script>
    import axios from 'axios';
    import EveImage from "./EveImage"

    export default {
        name: "SearchCorpOrAlliance",
        components: {EveImage},
        props: ['value'],
        data() {
            return {
                corpOrAlliances: [],
                search: '',
                selected: this.value,
                error: this.$page.props.errors.selectedEntities
            }
        },
        methods: {
            performSearch() {

                if (this.search.length < 3)
                    return

                axios
                .get(this.$route('search.alliance.corporation', this.search))
                    .then(results => {

                        this.corpOrAlliances = _.map(results.data, (result) => {

                            let object = {
                                id: result.id,
                                name: result.name,
                                category: result.category
                            }

                            if(result.category === 'corporation') {
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
            },
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
        },
        computed: {
            selectedIds() {
                return _.map(this.selected, (entity) => entity.id)
            }
        },
        watch: {
           selected(newValue) {
                this.$emit('input', newValue)
            }
        }
    }
</script>

<style scoped>

</style>
