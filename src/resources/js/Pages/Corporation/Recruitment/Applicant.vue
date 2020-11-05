<template>
    <li>
        <inertia-link :href="href" class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
            <div class="flex items-center px-4 py-4 sm:px-6">
                <div class="min-w-0 flex-1 flex items-center">
                    <div class="flex-shrink-0">
                        <EveImage tailwind_class="h-12 w-12 rounded-full" :size="256" :object="character" />
                        <!--<img class="h-12 w-12 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">-->
                    </div>
                    <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                        <div>
                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                {{ character.name }}
                            </div>
                            <!--<div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span class="truncate">ricardo.cooper@example.com</span>
                            </div>-->
                        </div>
                        <div class="hidden md:block">
                            <div>
                                <div v-if="isMissingScopes" class="flex items-center text-sm leading-5 text-gray-900">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    Missing following scopes: {{ getMissingScopesString(character.missing_scopes) }}
                                </div>
                                <div v-else class="flex items-center text-sm leading-5 text-gray-900">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    All requirements fulfilled
                                </div>
                                <!--<div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Completed phone screening
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <!--Third column-->
                    <svg v-if="href" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </inertia-link>
    </li>
</template>

<script>

import EveImage from "@/Shared/EveImage";

export default {
    name: "Applicant",
    components: {EveImage},
    props: {
        character: {
            required: true,
            type: Object
        },
        href: {
            required: false,
            type: String,
            default: ''
        }
    },
    computed: {
        isMissingScopes() {
            return !_.isEmpty(this.character.missing_scopes)
        }
    },
    methods: {
        getMissingScopesString(missing_scopes) {

          missing_scopes = _.isObject(missing_scopes) ? _.toArray(missing_scopes) : missing_scopes

          return _.toString(missing_scopes)
        }
    }
}
</script>

<style scoped>

</style>
