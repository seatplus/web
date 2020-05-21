<template>
    <div>
        <div class="bg-white overflow-hidden shadow rounded-lg mt-12">
            <div class="px-4 py-5 sm:p-6">

                <div>
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Settings
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            Setup the access group and define what anyone in this group should be able to achieve for which asset. F.e. if this is your recruiter access contol group you would define f.e. that a recruiter should have access to assets of characters in any but (inverse) a specific corporation. Note: you will be able to assign the access control group to users in another step.
                        </p>
                    </div>

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
                        <draggable :list="allowed" group="affiliations" :class="{'divide-y divide-grey-200': !isEmpty(allowed), 'h-20': isEmpty(allowed)}">
                            <div v-for="(entity, index) of allowed" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                            </div>
                        </draggable>
                    </div>

                </div>
            </div>
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white flex flex-col justify-between">
                    <div class="flex-1 p-6 ">
                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                            Inverse
                        </h3>
                        <p class="mt-3 text-base leading-6 text-gray-500">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                        </p>
                    </div>
                    <div :class="['bg-white overflow-hidden sm:rounded-md', {'shadow' : !isEmpty(inverse)}]">
                        <draggable :list="inverse" group="affiliations" :class="{'divide-y divide-grey-200': !isEmpty(inverse), 'h-20': isEmpty(inverse)}">
                            <div v-for="(entity, index) of inverse" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                            </div>
                        </draggable>
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
                        <draggable :list="forbidden" group="affiliations" :class="{'divide-y divide-grey-200': !isEmpty(forbidden), 'h-20': isEmpty(forbidden)}">
                            <div v-for="(entity, index) of forbidden" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                                <EveImage :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                            </div>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg mt-12 mb-12">
            <div class="px-4 py-5 sm:p-6">

                <div>
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Entities
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            Drag and drop the entities you wish to affiliate into their affiliation
                        </p>
                    </div>
                </div>

                <div class="m-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
                    <draggable :list="entities" group="affiliations">
                        <div v-for="(entity, index) of entities" :key="index" class="px-4 py-4 flex items-center sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                            <EveImage :object="entity" :size="128" show-name />
                        </div>
                    </draggable>

                </div>

            </div>
        </div>
    </div>

</template>

<script>
    import axios from "axios"
    import draggable from 'vuedraggable'
    import EveImage from "../../Shared/EveImage"

    export default {
        name: "Affiliations",
        components: {EveImage, draggable},
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
                moving: false
            }
        },
        methods: {
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
            onStart() {
                this.flipMoving()
            },
            onEnd() {
                this.flipMoving()
            },
            flipMoving() {
                this.moving = !this.moving
            },
            isEmpty(array) {
                return _.isEmpty(array)
            }
        },
        watch: {
            entities() {
                let affiliations = ['allowed', 'inverse', 'forbidden']

                _.each(affiliations, (affiliation) => this[affiliation] = this.getAffiliatedEntities(affiliation))
            }
            /*allowed() {
                return this.getAffiliatedEntities('allowed')
            },
            inverse() {
                return this.getAffiliatedEntities('inverse')
            },
            forbidden() {
                return this.getAffiliatedEntities('forbidden')
            }*/
        },
        mounted() {
            let routes = ['get.character_info', 'get.corporation_info', 'get.alliance_info']

            _.each(routes, (route) => this.getInfo(this.$route(route)).then(info => this.pushInfo(info)))
        }
    }
</script>

<style scoped>

</style>
