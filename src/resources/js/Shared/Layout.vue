<template>
    <!--<div>
        <div class="wrapper">
            <navbar />
            <sidebar :activeEntryUrl="getActiveSidebarElement()" />
            &lt;!&ndash; Content Wrapper. Contains page content &ndash;&gt;
            <div class="content-wrapper">
                &lt;!&ndash; Content Header (Page header) &ndash;&gt;
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0 text-dark">
                                    {{ this.pageHeader}}
                                    <small>{{ this.pageDescription}}</small>
                                </h1>
                            </div>&lt;!&ndash; /.col &ndash;&gt;

                            &lt;!&ndash;<div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Starter Page</li>
                                </ol>
                            </div>&ndash;&gt;&lt;!&ndash; /.col &ndash;&gt;
                        </div>&lt;!&ndash; /.row &ndash;&gt;
                    </div>&lt;!&ndash; /.container-fluid &ndash;&gt;
                </div>
                &lt;!&ndash; /.content-header &ndash;&gt;

                &lt;!&ndash; Main content &ndash;&gt;
                <section class="content">
                    <div class="container-fluid">
                        <b-card no-body v-if="$page.user.data.impersonating">
                            <inertia-link  :href="route('impersonate.stop')" class="btn-block btn btn-warning">Stop Impersonate</inertia-link>
                        </b-card>
                        <FlashMessages />
                        <RequiredScopesWarning :v-if="hasRequiredScopes()" :scopes="this.requiredScopes" />
                    </div>
                    <slot />

                </section>
                &lt;!&ndash; /.content &ndash;&gt;
            </div>
            &lt;!&ndash; /.content-wrapper &ndash;&gt;
            <Footer />

        </div>
    </div>-->
    <!--<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.9.3/dist/alpine.js" defer></script>-->

    <!--TODO: Include Alert Component-->


        <div class="h-screen flex overflow-hidden bg-gray-100" @keydown.window.escape="sidebarOpen = false">
            <!-- Off-canvas menu for mobile -->
            <div class="md:hidden">
                <div @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-600 opacity-0 pointer-events-none transition-opacity ease-linear duration-300" :class="{'opacity-75 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"></div>
                <div class="fixed inset-y-0 left-0 flex flex-col z-40 max-w-xs w-full bg-gray-800 transform ease-in-out duration-300 " :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                    <div class="absolute top-0 right-0 -mr-14 p-1">
                        <button v-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
                            <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex-shrink-0 flex items-center h-16 px-4 bg-gray-900">
                        <img class="h-8 w-auto" :src="$page.images.logo" alt="SeAT plus" />

                    </div>
                    <div class="flex-1 h-0 overflow-y-auto">
                        <sidebar :active-entry-url="getActiveSidebarElement()"/>
                    </div>
                </div>
            </div>

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
                            <button class="p-1 text-gray-400 rounded-full hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:shadow-outline focus:text-gray-500">
                                <!--<svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>-->

                                <span class="inline-block relative">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full text-white shadow-solid bg-red-400"></span>
                                </span>

                            </button>
                            <div class="ml-3 relative">
                                <!--User Menu-->
                                <Menu />
                            </div>
                        </div>
                    </div>
                </div>
                <main class="flex-1 relative z-0 overflow-y-auto py-6 focus:outline-none" tabindex="0">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Replace with your content -->
                        <slot/>
                        <!-- /End replace -->
                    </div>
                </main>
            </div>
        </div>

</template>

<script>
    import Sidebar from "@/Shared/Sidebar"
    import FlashMessages from "@/Shared/FlashMessages"
    import Footer from "@/Shared/Footer"
    import RequiredScopesWarning from "./RequiredScopesWarning"
    import Menu from "@/Shared/Menu"

    export default {
        name: "Layout",
        components: {
            Menu,
            FlashMessages,
            Sidebar,
            Footer,
            RequiredScopesWarning
        },
        props   : {
            pageHeader: {
                type: String,
                default: 'PAGE HEADER',
                required: true
            },
            pageDescription: {
                type: String,
                default: 'Page Description',
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
                menuOpen: false
            }
        },
        methods: {
            getActiveSidebarElement() {
                return this.activeSidebarElement ?? window.location.href
            },
            hasRequiredScopes() {
                return ! _.isEmpty(this.requiredScopes)
            },
        }
    }
</script>

<style scoped>

</style>
