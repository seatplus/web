<template>
    <div class="bg-white shadow-lg rounded-lg pointer-events-auto">
        <div class="rounded-lg shadow-xs overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24" v-html="this.icon">

                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm leading-5 font-medium text-gray-900">
                            {{ this.title }}
                        </p>
                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            {{ this.text }}
                        </p>
                        <div class="mt-2" v-if="this.link1 || this.link2">
                            <inertia-link v-if="this.link1" :href="this.route(this.payload.link1.route)" class="text-sm leading-5 font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">
                                {{ this.payload.link1.text }}
                            </inertia-link>
                            <inertia-link v-if="this.link2" :href="this.route(this.payload.link2.route)" class="ml-6 text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:underline transition ease-in-out duration-150">
                                {{ this.payload.link2.text }}
                            </inertia-link>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="$emit('remove', id)" class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Notification",
        props: ['id', 'payload'],
        computed: {
            title() {
                return this.payload.hasOwnProperty('title') ? this.payload.title : 'Title'
            },
            text() {
                return this.payload.hasOwnProperty('text') ? this.payload.title : 'Text'
            },
            icon() {
                return this.payload.hasOwnProperty('icon')
                    ? this.payload.icon
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            },
            link1() {
                if(this.payload.hasOwnProperty('link1')) {
                    return this.payload.link1.hasOwnProperty('route') && this.payload.link1.hasOwnProperty('text')
                }
                return false
            },
            link2() {
                if(this.payload.hasOwnProperty('link2')) {
                    return this.payload.link2.hasOwnProperty('route') && this.payload.link2.hasOwnProperty('text')
                }
                return false
            }
        }
    }
</script>

