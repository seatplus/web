<template>
    <li>
        <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
            <div class="flex items-center px-4 py-4 sm:px-6">
                <div class="min-w-0 flex-1 flex items-center">
                    <!--<div class="text-sm leading-5 font-medium text-indigo-600 truncate inline-flex items-center space-x-2">
                        <EveImage :object="{character_id: member.character_id}" :size="256" tailwind_class="h-6 w-6 rounded-full" />
                        <span v-if="hasCharacter(member)">{{ member.character.name }}</span>
                        <span v-else> {{ getCharacterName(member) }}</span>
                    </div>-->
                    <div class="flex-shrink-0">
                        <EveImage tailwind_class="h-12 w-12 rounded-full" :size="256" :object="character.character" />
                        <!--<img class="h-12 w-12 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">-->
                    </div>
                    <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                        <div class="self-center">
                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                {{ character.character.name }}
                            </div>
                        </div>
                        <div class=""><!--hidden md:block-->
                            <div>
                                <div v-if="isMissingScopes(character)" class="flex items-center text-sm leading-5 text-gray-900">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
import EveImage from "@/Shared/EveImage";
export default {
    name: "CharacterComplianceElement",
    components: {EveImage},
    props: {
        character: {
            required: true,
            type: Object
        }
    },
    methods: {
        getMissingScopesString(missing_scopes) {

            missing_scopes = _.isObject(missing_scopes) ? _.toArray(missing_scopes) : missing_scopes

            return _.toString(missing_scopes)
        },
        isMissingScopes(character) {
            return !_.isEmpty(character.missing_scopes)
        }
    }
}
</script>

<style scoped>

</style>
