<template>
    <div class="pb-5 border-b border-gray-200 space-y-2">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Job Postings
        </h3>
        <p class="max-w-4xl text-sm leading-5 text-gray-500">Workcation is a property rental website. Etiam ullamcorper massa viverra consequat, consectetur id nulla tempus. Fringilla egestas justo massa purus sagittis malesuada.</p>
        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="character in characters" :key="character.character_id" class="col-span-1 bg-white rounded-lg shadow">
                <div class="w-full flex items-center justify-between p-6 space-x-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-900 text-sm leading-5 font-medium truncate">{{ character.name }}</h3>
                            <span class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">{{ character.corporation.name }} {{ character.alliance ? ` - ${character.alliance.name}` : ''}} </span>
                        </div>
                        <!--<p class="mt-1 text-gray-500 text-sm leading-5 truncate">TODO: relevant infos here </p>-->
                    </div>
                    <EveImage tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" :object="character" :size="256" />
                    <!--<img class="" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">-->
                </div>
                <div class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <!--<div class="w-0 flex-1 flex border-r border-gray-200">
                            <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span class="ml-3">Email2</span>
                            </a>
                        </div>-->
                        <div class="-ml-px w-0 flex-1 flex">
                            <inertia-link :href="$route('delete.character.application', character.character_id)" method="delete" v-if="character.application" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-red-700 font-medium border border-transparent rounded-bl-lg hover:text-red-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 text-red-700" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3">Remove Application</span>
                            </inertia-link>
                            <CharacterApplication v-else :enlistments="enlistments" :character_id="character.character_id"/>
                        </div>
                    </div>
                </div>
            </li>

        </ul>
    </div>
</template>

<script>
import CharacterApplication from "./CharacterApplication"
import EveImage from "../../Shared/EveImage"
export default {
    name: "Characters",
    components: {EveImage, CharacterApplication},
    props: {
        characters: {
            type: Array
        },
        enlistments: {
            type: Array,
            default: []
        }
    },
    methods: {

    },

}
</script>

<style scoped>

</style>
