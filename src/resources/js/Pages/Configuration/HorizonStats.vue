<template>
    <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Horizon worker stats
        </h3>
        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                    Worker load
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl leading-8 font-semibold text-gray-900">
                                        {{stats.queue_count}}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm leading-5">
                        <a :href="route('horizon.index')" class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150">
                            View all
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <!--<svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                            </svg>-->
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                    Error count
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl leading-8 font-semibold text-gray-900">
                                        {{stats.error_count}}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm leading-5">
                        <a :href="route('horizon.index')" class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150">
                            View all
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <!--<svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                            </svg>-->
                            <svg v-if="stats.status === 'running'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg v-if="stats.status === 'inactive'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg v-if="stats.status === 'paused'" fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                    Status
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl leading-8 font-semibold text-gray-900 capitalize">
                                        {{stats.status}}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm leading-5">
                        <a :href="route('horizon.index')" class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150">
                            View all
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "HorizonStats",
        data() {
            return {
                stats: {}
            }
        },
        mounted() {
            this.refreshStats();
            setInterval(() => { this.refreshStats() }, 5000)
        },
        methods: {
            /**
             * Refresh the stats every period of time.
             */
            async refreshStats() {
                Promise.all([
                    // Make an ajax request to our server - /queue/status
                    await this.loadStats(),
                ]).catch(error => {
                    console.log(error);
                })
            },
            /*
             * load stats
             */
            loadStats() {
                return axios
                    .get('/queue/status')
                    .then( response =>
                        this.stats = response.data
                    ).catch(error => {
                        console.log(error);
                    });
            }
        },
        computed : {
            isLoading: function () {
                var obj = this.stats;
                for(var key in obj) {
                    if(obj.hasOwnProperty(key))
                        return false;
                }
                return true;
            }
        }
    }
</script>

<style scoped>

</style>
