<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">

            <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                {{ $I18n.trans('web::missing_required_scopes.title')}}
            </h2>
            <p class="mt-2 text-center text-sm leading-5 text-gray-600 max-w">
                {{ $I18n.trans('web::missing_required_scopes.description')}}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full md:max-w-sm">
            <!--List-->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul>
                    <li v-for="character in characters">
                        <a :href="character.upgrade_url" class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="flex items-center px-4 py-4 sm:px-6">
                                <div class="min-w-0 flex-1 flex items-center">
                                    <div class="flex-shrink-0">
                                        <eve-image :object="character" :size="256"/>
                                        <!--<img class="h-12 w-12 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />-->
                                    </div>
                                    <div class="min-w-0 flex-1 px-4">
                                        <div>
                                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">{{character.name}}</div>
                                            <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                    <path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>

                                                <span class="truncate">{{character.corporation}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <!--EndList-->
        </div>
    </div>
</template>

<script>
    import EveImage from "../../Shared/EveImage"
    export default {
        name: "MissingRequiredScopes",
        components: {EveImage},
        props: {
            characters: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style scoped>

</style>
