<template>
    <div :class="['shadow-lg rounded-lg pointer-events-auto border', {'bg-gray-50' : isDefault, 'bg-blue-50': isType('info'), 'bg-yellow-50': isType('warning'), 'bg-red-50': isType('error'), 'bg-green-50': isType('success')}]">
        <div class="rounded-lg shadow-xs overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg v-if="isDefault === 'outline'" :class="['h-6 w-6', {'text-gray-400' : isDefault, 'text-blue-400': isType('info'), 'text-yellow-400': isType('warning'), 'text-red-400': isType('error'), 'text-green-400': isType('success')}]" stroke="currentColor" fill="none" viewBox="0 0 24 24" v-html="this.icon"></svg>
                        <svg v-else :class="['h-6 w-6', {'text-gray-400' : isDefault, 'text-blue-400': isType('info'), 'text-yellow-400': isType('warning'), 'text-red-400': isType('error'), 'text-green-400': isType('success')}]" fill="currentColor" viewBox="0 0 20 20" v-html="this.icon"></svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p :class="['text-sm leading-5 font-medium', {'text-gray-800' : isDefault, 'text-blue-800': isType('info'), 'text-yellow-800': isType('warning'), 'text-red-800': isType('error'), 'text-green-800': isType('success')}]">
                            {{ this.title }}
                        </p>
                        <p :class="['mt-1 text-sm leading-5', {'text-gray-700' : isDefault, 'text-blue-700': isType('info'), 'text-yellow-700': isType('warning'), 'text-red-700': isType('error'), 'text-green-700': isType('success')}]">
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
                        <button @click="$emit('remove', id)" :class="['inline-flex focus:outline-none transition ease-in-out duration-150', {'text-gray-400 focus:text-gray-700' : isDefault, 'text-blue-400 focus:text-blue-700': isType('info'), 'text-yellow-400 focus:text-yellow-700': isType('warning'), 'text-red-400 focus:text-red-700': isType('error'), 'text-green-400 focus:text-green-700': isType('success')}]">
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
        data() {
            return {
                schemaLevels: ['info', 'warning', 'error', 'success'],
                predefinedIcons: {
                    info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
                    warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
                    error: ' <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />',
                    success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
                    // requires outline icon type
                    default: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                },
            }
        },
        methods: {
            isType(type) {
                return _.isEqual(type, this.type)
            },
        },
        computed: {
            type() {
                return this.schemaLevels.includes(this.payload.type) ? this.payload.type : 'default'
            },
            title() {
                return this.payload.hasOwnProperty('title') ? this.payload.title : 'Title'
            },
            text() {
                return this.payload.hasOwnProperty('text') ? this.payload.text : 'Text'
            },
            isDefault() {
                return !this.schemaLevels.includes(this.payload.type)
            },
            colorSchema() {

                const schemaLevels = ['info', 'warning', 'error', 'success']

                if(! schemaLevels.includes(this.payload.type))
                    return this.defaultColorSchema

                const typedSchema = this.colorSchemas[this.payload.type]

                return  {
                    button: `text-${typedSchema.color}-400 focus:text-${typedSchema.color}-700`
                }

            },
            icon() {

                return this.payload.hasOwnProperty('icon')
                    ? this.payload.icon
                    : this.predefinedIcons[this.type]
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

