<template>
    <!--TODO: Include Alert Component-->

    <div class="h-screen flex overflow-hidden bg-gray-100" @keydown.window.escape="sidebarOpen = false">

        <!-- Off-canvas menu for mobile -->
        <transition leave-active-class="duration-300">
            <div v-show="sidebarOpen" class="md:hidden">
                <div class="fixed inset-0 flex z-40">
                    <transition
                        enter-active-class="transition-opacity ease-linear duration-300"
                        enter-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition-opacity ease-linear duration-300"
                        leave-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div @click="sidebarOpen = false" v-show="sidebarOpen" class="fixed inset-0">
                            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
                        </div>
                    </transition>
                    <transition
                        enter-active-class="transition ease-in-out duration-300 transform"
                        enter-class="-translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition ease-in-out duration-300 transform"
                        leave-class="translate-x-0"
                        leave-to-class="-translate-x-full"
                    >
                        <div v-show="sidebarOpen"  class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gray-800">
                            <div class="absolute top-0 right-0 -mr-14 p-1">
                                <button v-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600" aria-label="Close sidebar">
                                    <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex-shrink-0 flex items-center px-4">
                                <img class="h-8 w-auto" :src="$page.images.logo" alt="SeAT plus"  />
                            </div>
                            <div class="mt-5 flex-1 h-0 overflow-y-auto">
                                <sidebar :active-entry-url="getActiveSidebarElement()"/>
                            </div>
                        </div>
                    </transition>
                    <div class="flex-shrink-0 w-14">
                        <!-- Dummy element to force sidebar to shrink to fit close icon -->
                    </div>
                </div>
            </div>
        </transition>



        <!-- Static sidebar for desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
                    <img class="h-8 w-auto" :src="$page.images.logo" alt="SeAT plus" />
                </div>
                <div class="h-0 flex-1 flex flex-col overflow-y-auto">
                    <sidebar :active-entry-url="getActiveSidebarElement()"/>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <button @click.stop="sidebarOpen = true" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 md:hidden">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <!--Search bar-->
                        <!--<div class="w-full flex md:ml-0">
                            <label for="search_field" class="sr-only">Search</label>
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input id="search_field" class="block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 sm:text-sm" placeholder="Search" />
                            </div>
                        </div>-->
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <NotificationsBell />
                        <div class="ml-3 relative">
                            <!--User Menu-->
                            <Menu />
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 relative z-0 overflow-y-auto py-6 focus:outline-none" tabindex="0">

                <Alerts />

                <Notifications class="md:mt-16 z-40" />

                <div v-if="isMissingRequiredScopes()" class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mb-3">
                    <RequiredScopesWarning :missing_characters_scopes="this.missing_characters_scopes" />
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mb-3">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ this.page}}</h1>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Replace with your content -->
                    <slot/>
                    <!-- /End replace -->
                </div>

                <!--<div class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5">
                    <div class="max-w-screen-xl mx-auto px-2 sm:px-6 lg:px-8">
                        <div class="p-2 rounded-lg bg-indigo-600 shadow-lg sm:p-3">
                            <div class="flex items-center justify-between flex-wrap">
                                <div class="w-0 flex-1 flex items-center">
                                  <span class="flex p-2 rounded-lg bg-indigo-800">
                                    <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                  </span>
                                    <p class="ml-3 font-medium text-white truncate">
                                        <span class="md:hidden">
                                          We announced a new product!
                                        </span>
                                                                    <span class="hidden md:inline">
                                          Big news! We're excited to announce a brand new product.
                                        </span>
                                    </p>
                                </div>
                                <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                                    <div class="rounded-md shadow-sm">
                                        <a href="#" class="flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-indigo-600 bg-white hover:text-indigo-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150">
                                            Learn more
                                        </a>
                                    </div>
                                </div>
                                <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
                                    <button type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:bg-indigo-500 transition ease-in-out duration-150">
                                        <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->

            </main>
            <transition leave-active-class="duration-300">
                <slot name="modal" />
            </transition>
        </div>
    </div>

</template>

<script>
    import Sidebar from "@/Shared/Sidebar"
    import RequiredScopesWarning from "./RequiredScopesWarning"
    import Menu from "@/Shared/Menu"
    import Notifications from "./Notifications"
    import NotificationsBell from "./NotificationsBell"
    import Alerts from "./Alerts"


    export default {
        name: "Layout",
        components: {
            Alerts,
            NotificationsBell,
            Notifications,
            Menu,
            Sidebar,
            RequiredScopesWarning
        },
        props   : {
            page: {
                type: String,
                default: 'PAGE HEADER',
                required: true
            },
            activeSidebarElement: {
                type: String,
                default: null,
                required: false,
            },
            requiredScopes: {
                type: Array,
                default: function () {
                    return []
                },
                required: false
            }
        },
        data() {
            return {
                sidebarOpen: false,
                menuOpen: false,
            }
        },
        methods: {
            getActiveSidebarElement() {
                return this.activeSidebarElement ?? window.location.href
            },
            isMissingRequiredScopes() {
                return ! _.isEmpty(this.missing_characters_scopes)
            },
        },
        computed: {
            missing_characters_scopes: function () {
                let returnValue = []
                let requiredScopes= this.requiredScopes

                _.forEach(this.$page.user.data.characters, function (character) {

                    let missing_scopes = _.difference(requiredScopes, character.scopes)

                    if(_.isEmpty(missing_scopes))
                        return

                    returnValue.push({
                        character_id: character.character_id,
                        name: character.name,
                        missing_scopes: missing_scopes
                    })
                })

                return returnValue;
            }
        },
    }
</script>

<style scoped>

</style>
