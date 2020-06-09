<template>
    <div>

        <div class="bg-white overflow-hidden shadow rounded-lg mt-12">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <!-- Content goes here -->
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Settings List
                </h3>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                    Setup the access group and define what anyone in this group should be able to achieve for which asset. F.e. if this is your recruiter access contol group you would define f.e. that a recruiter should have access to assets of characters in any but (inverse) a specific corporation. Note: you will be able to assign the access control group to users in another step.
                </p>
            </div>
            <div >
                <div class="w-full flex md:ml-0 px-6">
                    <label for="search_field" class="sr-only">
                        Search
                    </label>
                    <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                            </svg>
                        </div>
                        <input id="search_field" v-model="search" class="block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 sm:text-sm" placeholder="Search" type="search" />
                    </div>
                </div>

                <div class="overflow-auto overflow-x-hidden h-64">

                    <ListTransition :entries="filteredEntities">
                        <div class="flex flex-wrap" v-for="(entity, index) in filteredEntities" :key="entity.id">
                            <div  :class="[index%2 === 0 ? 'bg-white' : 'bg-gray-50', 'w-full sm:w-1/2 py-4 px-6']">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <EveImage :object="entity" tailwind_class="h-10 w-10 rounded-full" :size="128"/>
                                    </div>
                                    <div class="ml-4">
                                        <div class="whitespace-no-wrap leading-5 text-sm text-gray-900">
                                            {{ entity.name }}
                                        </div>
                                        <div class="text-sm leading-5 text-gray-500">
                                            bernardlane@example.com
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  :class="[index%2 === 0 ? 'bg-white' : 'bg-gray-50', 'w-full sm:w-1/2 py-4 px-6']">
                            <span class="relative z-0 inline-flex  rounded-md">
                                <span class="relative inline-flex items-center text-sm leading-5 font-medium text-gray-700 px-4">
                                    Add to ...
                                </span>
                                <button type="button" @click="addAffiliation('allowed', entity)" class="relative inline-flex shadow-sm items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Allowed
                                </button>
                                <button type="button" @click="addAffiliation('inverse', entity)" class="-ml-px relative inline-flex shadow-sm items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Inverse
                                </button>
                                <button type="button" @click="addAffiliation('forbidden', entity)" class="-ml-px relative inline-flex shadow-sm items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Forbidden
                                </button>
                            </span>
                            </div>
                        </div>
                    </ListTransition>

                </div>
            </div>
        </div>

        <div class="m-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white flex flex-col justify-between">
                    <div class="flex-1 p-6 ">
                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                            Allowed
                        </h3>
                        <p class="mt-3 text-base leading-6 text-gray-500">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                        </p>
                    </div>
                    <div :class="['bg-white overflow-hidden sm:rounded-md', {'shadow' : !isEmpty(allowed)}]">
                        <div class="divide-y divide-grey-200">
                            <ListTransition :entries="allowed">
                                <div v-for="(entity, index) of allowed" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                    <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                                </div>
                            </ListTransition>

                        </div>
                    </div>

                </div>
            </div>
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white flex flex-col justify-start">
                    <div class="flex-1 p-6 ">
                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                            Inverse
                        </h3>
                        <p class="mt-3 text-base leading-6 text-gray-500">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                        </p>
                    </div>
                    <div :class="['flex-1 bg-white sm:rounded-md', {'shadow' : !isEmpty(inverse)}]">
                        <ListTransition :entries="inverse">
                            <div v-for="(entity, index) of inverse" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                            </div>
                        </ListTransition>
                    </div>
                </div>
            </div>
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white flex flex-col justify-between">
                    <div class="flex-1 p-6 ">
                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                            Forbidden
                        </h3>
                        <p class="mt-3 text-base leading-6 text-gray-500">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                        </p>
                    </div>
                    <div :class="['bg-white overflow-hidden sm:rounded-md', {'shadow' : !isEmpty(forbidden)}]">
                        <ListTransition :entries="forbidden">
                            <div v-for="(entity, index) of forbidden" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                            </div>
                        </ListTransition>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    import axios from "axios"
    import EveImage from "../../Shared/EveImage"
    import ListTransition from "../../Shared/Transitions/ListTransition"

    export default {
        name: "Affiliations",
        components: {ListTransition, EveImage},
        props: {
            affiliations: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                entities: [],
                allowed: [],
                inverse: [],
                forbidden: [],
                search: null
            }
        },
        methods: {
            addAffiliation(type, entity) {
                this[type].unshift(entity)

                //delete this[type][this[type].indexOf(entity)]
            },
            getInfo: function (url, info = []) {
                return axios
                    .get(url)
                    .then(response => {

                        response.data.data.forEach(object => info.push(object))

                        if (_.isNull(response.data.links.next))
                            return info

                        return this.getInfo(response.data.links.next, info)
                    })
                    .catch(error => console.log(error))
            },
            pushInfo(info) {
                _.each(info, (entity) => {

                    _.has(entity,'alliance_id') ? entity.id = entity.alliance_id
                        : (_.has(entity,'corporation_id') ? entity.id = entity.corporation_id : entity.id = entity.character_id)

                    this.entities.push(entity)
                })
            },
            getAffiliatedEntities(type) {
                let filtered_affiliations =  _.filter(this.affiliations, (affiliation) => {return _.isEqual(affiliation.type, type)})

                return _.map(filtered_affiliations, (affiliation) => {
                    let id = _.isNumber(affiliation.alliance_id) ? affiliation.alliance_id
                        : (_.isNumber(affiliation.corporation_id) ? affiliation.corporation_id : affiliation.character_id)

                    return _.find(this.entities, {id: id})
                })
            },
            isEmpty(array) {
                return _.isEmpty(array)
            },
            sortByName(entities) {
                return _.sortBy(entities, (entity) => entity.name)
            }
        },
        computed: {
            filteredEntities() {

                let entities = _.filter(this.entities, (entity) => ![...this.allowed, ...this.inverse, ...this.forbidden].includes(entity))

                if(_.isNull(this.search))
                    return this.sortByName(entities)

                let term = this.search

                return this.sortByName(_.filter(entities, (entity) => entity.name.toUpperCase().includes(term.toUpperCase())))

            }
        },
        watch: {
            entities() {
                let affiliations = ['allowed', 'inverse', 'forbidden']

                _.each(affiliations, (affiliation) => this[affiliation] = this.getAffiliatedEntities(affiliation))
            },
        },
        mounted() {
            let routes = ['get.character_info', 'get.corporation_info', 'get.alliance_info']

            _.each(routes, (route) => this.getInfo(this.$route(route)).then(info => this.pushInfo(info)))
        }
    }
</script>

<style scoped>

</style>
