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
              <svg
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                class="h-6 w-6 text-white"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Worker load
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl leading-8 font-semibold text-gray-900">
                    {{ stats.queue_count }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm leading-5">
            <a
              :href="route('horizon.index')"
              class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150"
            >
              View all
            </a>
          </div>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg
                class="h-6 w-6 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
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
                    {{ stats.error_count }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm leading-5">
            <a
              :href="route('horizon.index')"
              class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150"
            >
              View all
            </a>
          </div>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                class="h-6 w-6 text-white"
                v-html="symbols[stats.status]"
              />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Status
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl leading-8 font-semibold text-gray-900 capitalize">
                    {{ stats.status }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm leading-5">
            <a
              :href="route('horizon.index')"
              class="font-medium text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150"
            >
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
                stats: {},
                timer: '',
                symbols: {
                    'running'   : '<path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'inactive'  : '<path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>',
                    'paused'    : '<path d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            }
        },
        mounted() {
            this.refreshStats()

            this.timer = setInterval(() => {
                this.refreshStats()
            }, 5000)
        },
        methods: {
            /**
             * Refresh the stats every period of time.
             */
            refreshStats: _.throttle(function() {
                this.loadStats()
            }, 5000),
            /*
             * load stats
             */
            loadStats() {
                return axios
                    .get('/queue/status')
                    .then( (response) => {
                        this.stats = response.data;
                    })
                    .catch(error => {
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
        },
        beforeUnmount () {
            clearInterval(this.timer)
        }
    }
</script>

<style scoped>

</style>
